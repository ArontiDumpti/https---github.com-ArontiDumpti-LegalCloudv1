<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\AI\VectorIndexService;
use App\Domain\Audit\AuditLogger;
use App\Domain\Documents\DocumentTextSearch;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SearchRequest;
use App\Http\Requests\Api\V1\UpdateDocumentRequest;
use App\Http\Requests\Api\V1\UploadDocumentRequest;
use App\Http\Requests\Api\V1\UploadDocumentVersionRequest;
use App\Http\Resources\Api\V1\DocumentResource;
use App\Http\Resources\Api\V1\DocumentVersionResource;
use App\Jobs\ProcessDocumentOcr;
use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\LegalCase;
use App\Models\ProcessingJob;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $documents = Document::query()->where('organization_id', $request->user()->organization_id)
            ->with('currentVersion')
            ->when($request->query('caseId'), fn (Builder $query, string $id) => $query->where('case_id', $id))
            ->when($request->query('categoryId'), fn (Builder $query, string $id) => $query->where('category_id', $id))
            ->when($request->query('status'), fn (Builder $query, string $status) => $query->where('status', $status))
            ->latest()->paginate(min(max((int) $request->query('perPage', 20), 1), 100));

        return DocumentResource::collection($documents);
    }

    public function store(UploadDocumentRequest $request, AuditLogger $audit): JsonResponse
    {
        $data = $request->validated();
        $file = $request->file('file');
        $path = null;

        try {
            $document = DB::transaction(function () use ($request, $audit, $data, $file, &$path): Document {
                $case = LegalCase::query()->where('organization_id', $request->user()->organization_id)->findOrFail($data['caseId']);
                $document = Document::query()->create([
                    'organization_id' => $request->user()->organization_id,
                    'case_id' => $case->id,
                    'category_id' => $data['categoryId'] ?? null,
                    'title' => $data['title'],
                    'description' => $data['description'] ?? null,
                    'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                    'size_bytes' => $file->getSize(),
                    'status' => 'processing',
                ]);
                $version = $this->persistVersion($document, $file, $request->user()->public_id, null, $path);
                $document->update(['current_version_id' => $version->id]);
                $job = $this->queueOcr($document, $version);
                $audit->record($request, $request->user(), 'document.uploaded', 'document', $document->id, ['versionId' => $version->id]);
                ProcessDocumentOcr::dispatch($job->id)->afterCommit();

                return $document->load('currentVersion');
            });
        } catch (Throwable $exception) {
            if ($path) {
                Storage::disk('private')->delete($path);
            }
            throw $exception;
        }

        return (new DocumentResource($document))->response()->setStatusCode(201);
    }

    public function show(Request $request, string $documentId): DocumentResource
    {
        return new DocumentResource($this->findDocument($request, $documentId)->load('currentVersion'));
    }

    public function update(UpdateDocumentRequest $request, string $documentId, AuditLogger $audit): DocumentResource
    {
        $document = $this->findDocument($request, $documentId);
        $data = $request->validated();
        $changes = [];
        foreach (['title', 'description'] as $field) {
            if (array_key_exists($field, $data)) {
                $changes[$field] = $data[$field];
            }
        }
        if (array_key_exists('categoryId', $data)) {
            $changes['category_id'] = $data['categoryId'];
        }
        $document->fill($changes)->save();
        $audit->record($request, $request->user(), 'document.updated', 'document', $document->id, $changes);

        return new DocumentResource($document->load('currentVersion'));
    }

    public function destroy(Request $request, string $documentId, AuditLogger $audit): JsonResponse
    {
        $document = $this->findDocument($request, $documentId);
        $document->delete();
        $audit->record($request, $request->user(), 'document.archived', 'document', $document->id);

        return response()->json(null, 204);
    }

    public function versions(Request $request, string $documentId)
    {
        $document = $this->findDocument($request, $documentId);

        return DocumentVersionResource::collection($document->versions()->get());
    }

    public function storeVersion(UploadDocumentVersionRequest $request, string $documentId, AuditLogger $audit): JsonResponse
    {
        $document = $this->findDocument($request, $documentId);
        $file = $request->file('file');
        $path = null;

        try {
            [$version, $job] = DB::transaction(function () use ($request, $audit, $document, $file, &$path): array {
                $lockedDocument = Document::query()->where('organization_id', $request->user()->organization_id)->lockForUpdate()->findOrFail($document->id);
                $version = $this->persistVersion($lockedDocument, $file, $request->user()->public_id, $request->validated('changeNote'), $path);
                $lockedDocument->update([
                    'current_version_id' => $version->id,
                    'mime_type' => $version->mime_type,
                    'size_bytes' => $version->size_bytes,
                    'status' => 'processing',
                ]);
                $job = $this->queueOcr($lockedDocument, $version);
                $audit->record($request, $request->user(), 'document.version_created', 'document', $lockedDocument->id, ['versionId' => $version->id]);
                ProcessDocumentOcr::dispatch($job->id)->afterCommit();

                return [$version, $job];
            });
        } catch (Throwable $exception) {
            if ($path) {
                Storage::disk('private')->delete($path);
            }
            throw $exception;
        }

        return (new DocumentVersionResource($version))->response()->setStatusCode(201);
    }

    public function download(Request $request, string $documentId, string $versionId)
    {
        $document = $this->findDocument($request, $documentId);
        $version = $document->versions()->whereKey($versionId)->firstOrFail();
        $storedFile = Storage::disk($version->disk)->get($version->path);
        $contents = (int) $version->file_encryption_version >= 1
            ? Crypt::decryptString($storedFile)
            : $storedFile;

        return response($contents, 200, [
            'Content-Type' => $version->mime_type,
            'Content-Length' => (string) strlen($contents),
            'Content-Disposition' => "attachment; filename*=UTF-8''".rawurlencode(basename($version->file_name)),
            'Cache-Control' => 'private, no-store, max-age=0',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; sandbox",
        ]);
    }

    public function search(SearchRequest $request, VectorIndexService $vectors, DocumentTextSearch $textSearch): JsonResponse
    {
        $data = $request->validated();
        $mode = $data['mode'] ?? 'hybrid';

        if ($mode === 'semantic' || ($mode === 'hybrid' && config('legalcloud.vector.enabled'))) {
            return response()->json($vectors->search(
                trim($data['query']),
                $request->user()->organization_id,
                $data,
                (int) ($data['page'] ?? 1),
                min(max((int) ($data['perPage'] ?? 20), 1), 100),
            ));
        }

        return response()->json($textSearch->search(
            trim($data['query']),
            $request->user()->organization_id,
            $data,
            (int) ($data['page'] ?? 1),
            min(max((int) ($data['perPage'] ?? 20), 1), 100),
        ));
    }

    private function findDocument(Request $request, string $documentId): Document
    {
        return Document::query()->where('organization_id', $request->user()->organization_id)->whereKey($documentId)->firstOrFail();
    }

    private function persistVersion(Document $document, mixed $file, string $uploadedBy, ?string $changeNote, ?string &$storedPath): DocumentVersion
    {
        $versionNumber = ((int) $document->versions()->max('version_number')) + 1;
        $storedPath = "organizations/{$document->organization_id}/documents/{$document->id}/version-{$versionNumber}-".Str::uuid().'.enc';
        $encryptedFile = Crypt::encryptString($file->get());
        if (! Storage::disk('private')->put($storedPath, $encryptedFile)) {
            throw new \RuntimeException('The document could not be stored.');
        }

        return $document->versions()->create([
            'version_number' => $versionNumber,
            'file_name' => basename($file->getClientOriginalName()),
            'disk' => 'private',
            'path' => $storedPath,
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
            'size_bytes' => $file->getSize(),
            'checksum' => hash_file('sha256', $file->getRealPath()),
            'change_note' => $changeNote,
            'uploaded_by' => $uploadedBy,
            'file_encryption_version' => 1,
        ]);
    }

    private function queueOcr(Document $document, DocumentVersion $version): ProcessingJob
    {
        return ProcessingJob::query()->create([
            'organization_id' => $document->organization_id,
            'document_id' => $document->id,
            'document_version_id' => $version->id,
            'type' => 'ocr',
            'status' => 'queued',
            'progress' => 0,
        ]);
    }
}

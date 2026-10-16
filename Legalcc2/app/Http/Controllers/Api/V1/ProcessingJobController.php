<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Audit\AuditLogger;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProcessingJobResource;
use App\Jobs\ProcessDocumentOcr;
use App\Models\Document;
use App\Models\ProcessingJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProcessingJobController extends Controller
{
    public function startOcr(Request $request, string $documentId, AuditLogger $audit): JsonResponse
    {
        $document = Document::query()->where('organization_id', $request->user()->organization_id)->findOrFail($documentId);

        if (! $document->current_version_id) {
            return response()->json(['code' => 'document_version_missing', 'message' => 'The document has no stored version.'], 409);
        }

        $activeJob = ProcessingJob::query()->where('document_version_id', $document->current_version_id)
            ->where('type', 'ocr')->whereIn('status', ['queued', 'processing'])->first();
        if ($activeJob) {
            return response()->json(['code' => 'processing_already_active', 'message' => 'OCR is already queued for this version.'], 409);
        }

        [$job] = DB::transaction(function () use ($request, $document, $audit): array {
            $job = ProcessingJob::query()->create([
                'organization_id' => $document->organization_id,
                'document_id' => $document->id,
                'document_version_id' => $document->current_version_id,
                'type' => 'ocr',
                'status' => 'queued',
            ]);
            $document->update(['status' => 'processing']);
            $audit->record($request, $request->user(), 'document.ocr_requested', 'document', $document->id, ['jobId' => $job->id]);
            ProcessDocumentOcr::dispatch($job->id)->afterCommit();

            return [$job];
        });

        return (new ProcessingJobResource($job))->response()->setStatusCode(202);
    }

    public function show(Request $request, string $jobId): ProcessingJobResource
    {
        $job = ProcessingJob::query()->where('organization_id', $request->user()->organization_id)->whereKey($jobId)->firstOrFail();

        return new ProcessingJobResource($job);
    }
}

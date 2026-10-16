<?php

namespace App\Jobs;

use App\Domain\AI\Contracts\OcrProvider;
use App\Domain\AI\ProviderNotConfigured;
use App\Models\Document;
use App\Models\DocumentChunk;
use App\Models\DocumentVersion;
use App\Models\ProcessingJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProcessDocumentOcr implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function __construct(public readonly string $processingJobId)
    {
        $this->onQueue('document-processing');
    }

    public function handle(OcrProvider $ocr): void
    {
        $job = ProcessingJob::query()->findOrFail($this->processingJobId);
        $version = DocumentVersion::query()->findOrFail($job->document_version_id);
        $job->update(['status' => 'processing', 'progress' => 10, 'started_at' => now()]);
        $temporaryPath = null;

        try {
            $storedContents = Storage::disk($version->disk)->get($version->path);
            $contents = (int) $version->file_encryption_version >= 1
                ? Crypt::decryptString($storedContents)
                : $storedContents;
            $temporaryPath = tempnam(storage_path('app/private'), '.ocr-');
            if ($temporaryPath === false || file_put_contents($temporaryPath, $contents) === false) {
                throw new \RuntimeException('Unable to prepare a private temporary file for OCR.');
            }
            @chmod($temporaryPath, 0600);

            $result = $ocr->extract($temporaryPath, $version->mime_type);
            $version->update(['extracted_text' => $result['text']]);

            // Persist provider-neutral chunks now; embeddings can be added without changing OCR storage.
            foreach ($result['pages'] as $page => $content) {
                foreach (array_chunk(preg_split('/\s+/u', trim($content)) ?: [], 240) as $index => $words) {
                    if ($words === []) {
                        continue;
                    }
                    DocumentChunk::query()->create([
                        'document_version_id' => $version->id,
                        'chunk_index' => $page * 10000 + $index,
                        'page' => $page + 1,
                        'content' => implode(' ', $words),
                    ]);
                }
            }

            $job->update(['status' => 'completed', 'progress' => 100, 'result_text' => $result['text'], 'completed_at' => now()]);
            Document::query()->whereKey($job->document_id)->update(['status' => 'indexed']);
            if (config('legalcloud.vector.enabled')) {
                IndexDocumentEmbeddings::dispatch($version->id);
            }
        } catch (ProviderNotConfigured) {
            $job->update(['status' => 'failed', 'progress' => 0, 'error_code' => 'ocr_provider_unavailable', 'completed_at' => now()]);
            Document::query()->whereKey($job->document_id)->update(['status' => 'failed']);
        } finally {
            if (is_string($temporaryPath) && is_file($temporaryPath)) {
                unlink($temporaryPath);
            }
        }
    }

    public function failed(?Throwable $exception): void
    {
        $job = ProcessingJob::query()->find($this->processingJobId);
        if (! $job) {
            return;
        }

        $job->update(['status' => 'failed', 'error_code' => 'ocr_processing_failed', 'completed_at' => now()]);
        Document::query()->whereKey($job->document_id)->update(['status' => 'failed']);
    }
}

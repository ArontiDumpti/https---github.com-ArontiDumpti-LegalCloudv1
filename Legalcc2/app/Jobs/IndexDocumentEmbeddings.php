<?php

namespace App\Jobs;

use App\Domain\AI\VectorIndexService;
use App\Models\DocumentChunk;
use App\Models\DocumentVersion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IndexDocumentEmbeddings implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function __construct(public readonly string $documentVersionId)
    {
        $this->onQueue('document-processing');
    }

    public function handle(VectorIndexService $vectors): void
    {
        if (! config('legalcloud.vector.enabled')) {
            return;
        }

        DocumentVersion::query()->findOrFail($this->documentVersionId);

        DocumentChunk::query()->where('document_version_id', $this->documentVersionId)
            ->orderBy('chunk_index')
            ->chunkById(100, function ($chunks) use ($vectors): void {
                foreach ($chunks as $chunk) {
                    $vectors->index($chunk);
                }
            });
    }
}

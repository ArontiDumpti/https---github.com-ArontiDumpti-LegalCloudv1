<?php

namespace App\Domain\AI;

use App\Domain\AI\Contracts\LlmProvider;
use App\Models\DocumentChunk;
use Illuminate\Database\Eloquent\Builder;

class RagAnswerService
{
    public function __construct(
        private readonly LlmProvider $llm,
        private readonly VectorIndexService $vectors,
    ) {}

    /** @return array{content: string, citations: list<array<string, mixed>>} */
    public function answer(string $question, string $organizationId, array $filters = [], string $mode = 'answer'): array
    {
        $sources = $this->retrieve($question, $organizationId, $filters);
        if ($sources === []) {
            return [
                'content' => 'No encontré fragmentos indexados y autorizados que permitan responder esta pregunta.',
                'citations' => [],
            ];
        }

        return [
            'content' => $this->llm->answer($question, $sources, $mode),
            'citations' => array_map(fn (array $source) => $source['citation'], $sources),
        ];
    }

    /** @return list<array{content: string, citation: array<string, mixed>}> */
    private function retrieve(string $question, string $organizationId, array $filters): array
    {
        if (config('legalcloud.vector.enabled')) {
            $results = $this->vectors->search($question, $organizationId, $filters, 1, 5);

            return collect($results['data'])->values()->map(function (array $hit, int $index): array {
                $document = $hit['document'];
                $version = $document['currentVersion'];

                return [
                    'content' => $hit['excerpt'],
                    'citation' => [
                        'reference' => $index + 1,
                        'documentId' => $document['id'],
                        'versionId' => $version['id'],
                        'title' => $document['title'],
                        'page' => $hit['page'],
                        'excerpt' => $hit['excerpt'],
                        'relevance' => $hit['score'],
                    ],
                ];
            })->all();
        }

        $terms = collect(preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($question)) ?: [])
            ->filter(fn (string $term) => mb_strlen($term) >= 3)
            ->unique()->take(10)->values();

        if ($terms->isEmpty()) {
            return [];
        }

        $chunks = DocumentChunk::query()
            ->with('version.document')
            ->whereHas('version.document', function (Builder $documents) use ($organizationId, $filters): void {
                $documents->where('organization_id', $organizationId)
                    ->whereColumn('documents.current_version_id', 'document_versions.id')
                    ->when($filters['caseId'] ?? null, fn (Builder $query, string $caseId) => $query->where('case_id', $caseId))
                    ->when($filters['documentIds'] ?? null, fn (Builder $query, array $ids) => $query->whereIn('documents.id', $ids));
            })
            ->orderBy('id')->limit(5000)->get();

        return $chunks->map(function (DocumentChunk $chunk) use ($terms): array {
            $version = $chunk->version;
            $document = $version?->document;
            if (! $version || ! $document) {
                return null;
            }

            $content = $chunk->content;
            $matches = $terms->filter(fn (string $term) => mb_stripos($content, $term) !== false)->count();
            if ($matches === 0) {
                return null;
            }

            return [
                'content' => mb_substr($content, 0, 2400),
                'score' => $matches / max($terms->count(), 1),
                'citation' => [
                    'documentId' => $document->id,
                    'versionId' => $version->id,
                    'title' => $document->title,
                    'page' => $chunk->page,
                    'excerpt' => mb_substr($content, 0, 800),
                    'relevance' => $matches / max($terms->count(), 1),
                ],
            ];
        })->filter()->sortByDesc('score')->take(5)->map(function (array $source, int $index): array {
            $source['citation']['reference'] = $index + 1;
            unset($source['score']);

            return $source;
        })->values()->all();
    }
}

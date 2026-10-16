<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Audit\AuditLogger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreCaseRequest;
use App\Http\Requests\Api\V1\UpdateCaseRequest;
use App\Http\Resources\Api\V1\CaseResource;
use App\Models\LegalCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class CaseController extends Controller
{
    public function index(Request $request)
    {
        $query = LegalCase::query()->where('organization_id', $request->user()->organization_id)
            ->withCount('documents')
            ->when($request->query('status'), fn ($query, $status) => $query->where('status', $status))
            ->when($request->query('categoryId'), fn ($query, $id) => $query->where('category_id', $id));
        $perPage = min(max((int) $request->query('perPage', 20), 1), 100);
        $page = max((int) $request->query('page', 1), 1);
        $search = trim((string) $request->query('search', ''));
        if ($search === '') {
            return CaseResource::collection($query->orderByDesc('updated_at')->paginate($perPage));
        }

        $referenceHash = LegalCase::referenceLookupHash($search);
        $cases = $query->orderByDesc('updated_at')->limit(5000)->get()
            ->filter(fn (LegalCase $case) => mb_stripos($case->title, $search) !== false
                || (is_string($case->reference_hash) && hash_equals($case->reference_hash, $referenceHash)))->values();
        $total = $cases->count();
        $cases = new LengthAwarePaginator(
            $cases->slice(($page - 1) * $perPage, $perPage)->values(),
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );

        return CaseResource::collection($cases);
    }

    public function store(StoreCaseRequest $request, AuditLogger $audit): JsonResponse
    {
        $data = $request->validated();
        $case = LegalCase::query()->create([
            'organization_id' => $request->user()->organization_id,
            'category_id' => $data['categoryId'] ?? null,
            'owner_id' => $data['ownerId'] ?? $request->user()->public_id,
            'reference' => $data['reference'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => 'draft',
        ]);

        $audit->record($request, $request->user(), 'case.created', 'case', $case->id);

        return (new CaseResource($case->loadCount('documents')))->response()->setStatusCode(201);
    }

    public function show(Request $request, string $caseId): CaseResource
    {
        return new CaseResource($this->findInOrganization($request, $caseId)->loadCount('documents'));
    }

    public function update(UpdateCaseRequest $request, string $caseId, AuditLogger $audit): CaseResource
    {
        $case = $this->findInOrganization($request, $caseId);
        $data = $request->validated();
        $changes = [];
        foreach (['title', 'description', 'status'] as $field) {
            if (array_key_exists($field, $data)) {
                $changes[$field] = $data[$field];
            }
        }
        if (array_key_exists('categoryId', $data)) {
            $changes['category_id'] = $data['categoryId'];
        }
        if (array_key_exists('ownerId', $data)) {
            $changes['owner_id'] = $data['ownerId'];
        }
        $case->fill($changes)->save();
        $audit->record($request, $request->user(), 'case.updated', 'case', $case->id, $changes);

        return new CaseResource($case->loadCount('documents'));
    }

    private function findInOrganization(Request $request, string $caseId): LegalCase
    {
        return LegalCase::query()->where('organization_id', $request->user()->organization_id)->whereKey($caseId)->firstOrFail();
    }
}

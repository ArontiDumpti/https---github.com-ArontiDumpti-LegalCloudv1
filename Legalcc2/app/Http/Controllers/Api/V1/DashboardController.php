<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\AuditEventResource;
use App\Models\AuditEvent;
use App\Models\Document;
use App\Models\LegalCase;
use App\Models\ProcessingJob;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function show(Request $request)
    {
        $organizationId = $request->user()->organization_id;
        $canSeeAudit = in_array($request->user()->role, ['administrator', 'records_manager'], true);

        return response()->json([
            'cases' => [
                'total' => LegalCase::query()->where('organization_id', $organizationId)->count(),
                'open' => LegalCase::query()->where('organization_id', $organizationId)->where('status', 'open')->count(),
                'inReview' => LegalCase::query()->where('organization_id', $organizationId)->where('status', 'in_review')->count(),
            ],
            'documents' => [
                'total' => Document::query()->where('organization_id', $organizationId)->count(),
                'indexed' => Document::query()->where('organization_id', $organizationId)->where('status', 'indexed')->count(),
                'pendingOcr' => Document::query()->where('organization_id', $organizationId)->where('status', 'processing')->count(),
            ],
            'processing' => [
                'queued' => ProcessingJob::query()->where('organization_id', $organizationId)->where('status', 'queued')->count(),
                'failed' => ProcessingJob::query()->where('organization_id', $organizationId)->where('status', 'failed')->count(),
            ],
            'recentActivity' => $canSeeAudit ? AuditEventResource::collection(
                AuditEvent::query()->where('organization_id', $organizationId)->orderByDesc('occurred_at')->limit(5)->get(),
            )->resolve() : [],
        ]);
    }
}

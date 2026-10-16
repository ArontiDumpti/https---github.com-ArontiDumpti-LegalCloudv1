<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Audit\AuditLogger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreCategoryRequest;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        return CategoryResource::collection(Category::query()->where('organization_id', $request->user()->organization_id)->orderBy('name')->get());
    }

    public function store(StoreCategoryRequest $request, AuditLogger $audit): JsonResponse
    {
        $category = Category::query()->create([...$request->validated(), 'organization_id' => $request->user()->organization_id]);
        $audit->record($request, $request->user(), 'category.created', 'category', $category->id);

        return (new CategoryResource($category))->response()->setStatusCode(201);
    }
}

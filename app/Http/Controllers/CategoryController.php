<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryFilterRequest;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Services\CategoryService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private CategoryService $categoryService
    ) {}

    public function index(CategoryFilterRequest $request)
    {
        $result = $this->categoryService->getAllCategories($request->validated());

        return $this->successResponsePaginated($result);
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->createCategory($request->validated());

        return $this->successResponseWithMessage($category, 'Category created successfully', Response::HTTP_CREATED);
    }

    public function show(int $id, Request $request)
    {
        $options = $request
            ->merge(['products' => $request->boolean('products', false)])
            ->validate(['products' => 'boolean']);

        $category = $this->categoryService->getCategoryById($id, $options);

        if (empty($category)) {
            return $this->errorResponse('Category not found', Response::HTTP_NOT_FOUND);
        }

        return $this->successResponse($category);
    }

    public function update(UpdateCategoryRequest $request, int $id)
    {
        $category = $this->categoryService->updateCategory($id, $request->validated());

        if (!$category) {
            return $this->errorResponse('Category not found', Response::HTTP_NOT_FOUND);
        }

        return $this->successResponse($category);
    }

    public function destroy(int $id, Request $request)
    {
        $deletedBy = $request->user()->email ?? 'system';
        $category = $this->categoryService->deleteCategory($id, $deletedBy);

        if (!$category) {
            return $this->errorResponse('Category not found', Response::HTTP_NOT_FOUND);
        }

        return $this->successMessage('Category deleted successfully');
    }
}

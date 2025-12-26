<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\ProductFilterRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\ProductService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected ProductService $productService
    ) {}

    public function allProducts(ProductFilterRequest $request)
    {
        $result = $this->productService->getAllProducts($request->validated());

        return $this->successResponsePaginated($result);
    }

    public function productById(int $id, Request $request)
    {
        $options['categories'] = $request->boolean('categories', false);

        return $this->successResponse($this->productService->getProductById($id, $options));
    }

    public function store(CreateProductRequest $request)
    {
        $result = $this->productService->createProduct($request->validated());

        return $this->successResponseWithMessage($result, 'Product created successfully', Response::HTTP_CREATED);
    }

    public function update(int $id, UpdateProductRequest $request)
    {
        $result = $this->productService->updateProduct($id, $request->validated());

        if ($result) {
            return $this->successResponseWithMessage($result, 'Product updated successfully');
        }

        return $this->errorResponse('Product not found', Response::HTTP_NOT_FOUND);
    }

    public function delete(int $id)
    {
        $currentUser = Auth::user()->id;
        $result = $this->productService->deleteProduct($id, $currentUser);

        if ($result) {
            return $this->successMessage('Product has been deleted');
        }

        return $this->errorResponse('Product not found', Response::HTTP_NOT_FOUND);
    }
}

<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class ProductService
{
    public function getAllProducts(array $filters): LengthAwarePaginator
    {
        $perPage       = Arr::get($filters, 'per_page', 10);
        $categories    = Arr::get($filters, 'categories');
        $search        = Arr::get($filters, 'search');
        $status        = Arr::get($filters, 'status');
        $minPrice      = Arr::get($filters, 'min_price');
        $maxPrice      = Arr::get($filters, 'max_price');
        $categoryId    = Arr::get($filters, 'category_id');
        $sortBy        = Arr::get($filters, 'sort_by', 'updated_at');
        $sortDirection = Arr::get($filters, 'sort_dir', 'desc');

        $query = Product::query()
            ->when($categories, fn($q) => $q->with('categories'))
            ->when($search, fn($q) => $q->where('name', 'like', '%' . $search . '%'))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($minPrice, fn($q) => $q->where('price', '>=', $minPrice))
            ->when($maxPrice, fn($q) => $q->where('price', '<=', $maxPrice))
            ->when($categoryId, fn($q) => $q->whereHas('categories', fn($cat) => $cat->where('id', $categoryId)))
            ->when($sortBy && $sortDirection, fn($q) => $q->orderBy($sortBy, $sortDirection));

        return $query->paginate($perPage)->withQueryString();
    }

    public function getProductById(int $id, array $options = [])
    {
        $showCategories = $options['categories'];
        logger()->info('Fetching product by ID', ['id' => $id, 'options' => $options]);

        return Product::when($showCategories, fn($q) => $q->with('categories'))
            ->find($id) ?? [];
    }

    public function createProduct(array $data): Product
    {
        logger()->info('Creating product', ['data' => $data]);
        return Product::create($data);
    }

    public function updateProduct(int $id, array $data): ?Product
    {
        $product = Product::find($id);
        if ($product) {
            logger()->info('Updating product', ['id' => $id, 'data' => $data]);
            $product->update(array_merge($data, ['updated_at' => now()]));
            return $product;
        }

        return null;
    }

    public function deleteProduct(int $id, string $deletedBy)
    {
        $product = Product::find($id);
        if ($product) {
            logger()->info('Deleting product', ['id' => $id, 'deleted_by' => $deletedBy]);
            $product->update(['updated_by' => $deletedBy]);
            $product->delete();
            return $product;
        }

        return null;
    }
}

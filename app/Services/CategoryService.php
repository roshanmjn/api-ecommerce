<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CategoryService
{
    public function getAllCategories(array $filters): LengthAwarePaginator
    {
        $perPage       = Arr::get($filters, 'per_page', 10);
        $search        = Arr::get($filters, 'search');
        $status        = Arr::get($filters, 'status');
        $products      = Arr::get($filters, 'products');
        $productId     = Arr::get($filters, 'product_id');
        $sortBy        = Arr::get($filters, 'sort_by', 'updated_at');
        $sortDirection = Arr::get($filters, 'sort_dir', 'desc');

        $search = $search ? Str::lower(trim($search)) : null;
        $status = $status ? Str::lower(trim($status)) : null;

        $sortableColumns = ['updated_at', 'created_at', 'name', 'status'];
        if (!in_array($sortBy, $sortableColumns, true)) {
            $sortBy = 'updated_at';
        }
        $sortDirection = strtolower($sortDirection) === 'asc' ? 'asc' : 'desc';

        $query = Category::query()
            ->when($search, fn($q) => $q->where('name', 'like', '%' . $search . '%'))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($products, fn($q) => $q->with('products'))
            ->when($productId, fn($q) => $q->whereHas('products', fn($product) => $product->where('products.id', $productId)))
            ->orderBy($sortBy, $sortDirection);

        return $query->paginate($perPage)->withQueryString();
    }

    public function getCategoryById(int $id, array $options = [])
    {
        $showProducts = $options['products'] ?? false;
        logger()->info('Fetching category by ID', ['id' => $id, 'options' => $options]);

        return Category::when($showProducts, fn($q) => $q->with('products'))
            ->find($id) ?? [];
    }

    public function createCategory(array $data): Category
    {
        logger()->info('Creating category', ['data' => $data]);
        $data['updated_by'] = $data['created_by'];

        return Category::create($data);
    }

    public function updateCategory(int $id, array $data): ?Category
    {
        $category = Category::find($id);
        if ($category) {
            logger()->info('Updating category', ['id' => $id, 'data' => $data]);
            $category->update(array_merge($data, ['updated_at' => now()]));
            return $category;
        }

        return null;
    }

    public function deleteCategory(int $id, string $deletedBy): ?Category
    {
        $category = Category::find($id);
        if ($category) {
            logger()->info('Deleting category', ['id' => $id, 'deleted_by' => $deletedBy]);
            $category->update(['updated_by' => $deletedBy]);
            $category->delete();
            return $category;
        }

        return null;
    }
}

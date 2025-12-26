<?php

namespace App\Models;

use App\Enums\ProductStatus;
use App\Enums\ProductVisibility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'status',
        'visibility',
        'parent_id',
        'meta_title',
        'meta_description',
        'sort_order',
        'updated_at',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'status'     => ProductStatus::class,
        'visibility' => ProductVisibility::class,
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_categories')
            ->withPivot(['is_primary', 'sort_order'])
            ->withTimestamps()
            ->orderBy('pivot_sort_order');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }

    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }
}

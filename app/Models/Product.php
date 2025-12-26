<?php

namespace App\Models;

use App\Enums\ProductStatus;
use App\Enums\ProductVisibility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    public $incrementing = true;
    public $primaryKey = 'id';

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'sku',
        'price',
        'compare_price',
        'cost_price',
        'status',
        'visibility',
        'weight',
        'length',
        'width',
        'height',
        'track_inventory',
        'allow_backorder',
        'meta_title',
        'meta_description',
        'is_featured',
        'sort_order',
        'updated_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => ProductStatus::class,
        'visibility' => ProductVisibility::class,
        // 'price' => 'decimal:2',
        // 'compare_price' => 'decimal:2',
        // 'cost_price' => 'decimal:2',
        // 'weight' => 'decimal:3',
        // 'length' => 'decimal:2',
        // 'width' => 'decimal:2',
        // 'height' => 'decimal:2',
        // 'track_inventory' => 'boolean',
        // 'allow_backorder' => 'boolean',
        // 'is_featured' => 'boolean',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories')
            ->withPivot(['is_primary', 'sort_order'])
            ->withTimestamps()
            ->orderBy('pivot_sort_order');
    }

    public function primaryCategory()
    {
        return $this->categories()->wherePivot('is_primary', true)->first();
    }
}

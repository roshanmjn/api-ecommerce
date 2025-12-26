<?php

namespace App\Http\Requests;

use App\Enums\ProductStatus;
use App\Enums\ProductVisibility;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'              => 'sometimes|string|max:100',
            'description'       => 'nullable|string',
            'short_description' => 'nullable|string|max:255',
            'price'             => 'sometimes|numeric|min:0',
            'category_id'       => 'sometimes|exists:categories,id',
            'status'            => ['required', new Enum(ProductStatus::class)],
            'slug'              => 'nullable|string',
            'sku'               => 'nullable|string',
            'compare_price'     => 'nullable|numeric|min:0',
            'cost_price'        => 'nullable|numeric|min:0',
            'visibility'        => ['sometimes', new Enum(ProductVisibility::class)],
            'weight'            => 'nullable|numeric|min:0',
            'length'            => 'nullable|numeric|min:0',
            'width'             => 'nullable|numeric|min:0',
            'height'            => 'nullable|numeric|min:0',
            'track_inventory'   => 'nullable|boolean',
            'allow_backorder'   => 'nullable|boolean',
            'meta_title'        => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string|max:255',
            'is_featured'       => 'nullable|boolean',
            'sort_order'        => 'nullable|integer',
        ];
    }
    public function messages(): array
    {
        return [
            'status'     . Enum::class => 'Status must be one of: ' . $this->enumValues(ProductStatus::class),
            'visibility' . Enum::class => 'Visibility must be one of: ' . $this->enumValues(ProductVisibility::class),
        ];
    }

    private function enumValues(string $enum): string
    {
        return implode(
            ', ',
            array_map(fn($case) => $case->value, $enum::cases())
        );
    }
}

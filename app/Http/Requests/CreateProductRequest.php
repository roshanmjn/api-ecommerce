<?php

namespace App\Http\Requests;

use App\Enums\ProductStatus;
use App\Enums\ProductVisibility;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CreateProductRequest extends FormRequest
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
            'name'              => 'required|string|max:100',
            'description'       => 'nullable|string',
            'short_description' => 'nullable|string|max:255',
            'price'             => 'required|numeric|min:0',
            'category_id'       => 'required|exists:categories,id',
            'status'            => ['required', new Enum(ProductStatus::class)],
            'slug'              => 'sometimes|nullable|string',
            'sku'               => 'sometimes|nullable|string',
            'compare_price'     => 'sometimes|nullable|numeric|min:0',
            'cost_price'        => 'sometimes|nullable|numeric|min:0',
            'visibility'        => ['sometimes', new Enum(ProductVisibility::class)],
            'weight'            => 'sometimes|nullable|numeric|min:0',
            'length'            => 'sometimes|nullable|numeric|min:0',
            'width'             => 'sometimes|nullable|numeric|min:0',
            'height'            => 'sometimes|nullable|numeric|min:0',
            'track_inventory'   => 'sometimes|nullable|boolean',
            'allow_backorder'   => 'sometimes|nullable|boolean',
            'meta_title'        => 'sometimes|nullable|string|max:255',
            'meta_description'  => 'sometimes|nullable|string|max:255',
            'is_featured'       => 'sometimes|nullable|boolean',
            'sort_order'        => 'sometimes|nullable|integer',
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

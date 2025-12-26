<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryFilterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    function prepareForValidation()
    {
        $this->merge([
            'products' => $this->boolean('products', false),
        ]);
    }

    public function rules()
    {
        return [
            'per_page'   => 'sometimes|integer|min:1',
            'products'   => 'sometimes|boolean',
            'search'     => 'sometimes|string|nullable',
            'status'     => 'sometimes|string|nullable',
            'product_id' => 'sometimes|integer|nullable',
            'sort_by'    => 'sometimes|string|nullable',
            'sort_dir'   => 'sometimes|in:asc,desc|nullable',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductFilterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'per_page'    => 'sometimes|integer|min:1',
            'categories'  => 'sometimes|boolean|in:0,1',
            'search'      => 'sometimes|string|nullable',
            'status'      => 'sometimes|string|nullable',
            'min_price'   => 'sometimes|numeric|nullable',
            'max_price'   => 'sometimes|numeric|nullable',
            'category_id' => 'sometimes|integer|nullable',
            'sort_by'     => 'sometimes|string|nullable',
            'sort_dir'    => 'sometimes|in:asc,desc|nullable',
        ];
    }
}

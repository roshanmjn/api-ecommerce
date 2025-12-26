<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'             => 'sometimes|string|max:255',
            'slug'             => 'sometimes|string|max:255',
            'description'      => 'sometimes|nullable|string',
            'status'           => 'required|in:active,inactive',
            'visibility'       => 'sometimes|in:visible,hidden,search_only',
            'parent_id'        => 'sometimes|nullable|integer',
            'meta_title'       => 'sometimes|nullable|string|max:255',
            'meta_description' => 'sometimes|nullable|string',
            'sort_order'       => 'sometimes|nullable|integer',
            'updated_by'       => 'required|string',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'             => 'required|string|max:255',
            'slug'             => 'required|string|max:255|unique:categories,slug',
            'description'      => 'nullable|string',
            'image'            => 'nullable|string',
            'status'           => 'required|in:active,inactive',
            'visibility'       => 'sometimes|in:visible,hidden,search_only',
            'parent_id'        => 'nullable|exists:categories,id',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'sort_order'       => 'nullable|integer',
            'created_by'       => 'required|string',

        ];
    }
}

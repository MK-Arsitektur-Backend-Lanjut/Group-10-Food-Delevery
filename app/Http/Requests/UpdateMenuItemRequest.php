<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'menu_category_id' => ['nullable', 'integer', 'exists:menu_categories,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'is_available' => ['nullable', 'boolean'],
            'prep_time_minutes' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'image_url' => ['nullable', 'url', 'max:255'],
        ];
    }
}

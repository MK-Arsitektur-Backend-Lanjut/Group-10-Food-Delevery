<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRestaurantOperationalStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_open' => ['required', 'boolean'],
            'reason' => ['nullable', 'string', 'max:255'],
            'changed_by' => ['nullable', 'string', 'max:255'],
        ];
    }
}

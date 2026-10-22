<?php

namespace App\Http\Requests\Lms;

use Illuminate\Foundation\Http\FormRequest;

class StoreGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'cover'       => 'nullable|array|max:2',
            'cover.*'     => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ];
    }
}
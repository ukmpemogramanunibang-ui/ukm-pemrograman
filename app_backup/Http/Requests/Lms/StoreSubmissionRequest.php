<?php

namespace App\Http\Requests\Lms;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubmissionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'answer_text' => ['nullable', 'string', 'max:20000'],
            'answer_link' => ['nullable', 'url', 'starts_with:https://'],
            'note'        => ['nullable', 'string', 'max:1000'],
            'files'       => ['nullable', 'array', 'max:5'],
            'files.*'     => ['file', 'mimes:jpeg,jpg,png,webp,gif,pdf,docx,xlsx,zip', 'max:30720'],
        ];
    }
}
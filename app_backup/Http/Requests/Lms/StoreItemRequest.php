<?php

namespace App\Http\Requests\Lms;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['task', 'material'])],

            'group_id' => [
                'required',
                'integer',
                Rule::exists('lms_groups', 'id')->where(function ($q) {
                    $q->where('status', 'active');
                }),
            ],

            'title' => ['required', 'string', 'min:3', 'max:180'],
            'description' => ['nullable', 'string', 'max:8000'],

            'deadline_mode' => ['required', Rule::in(['limited', 'unlimited'])],
            'deadline_at'   => ['nullable', 'date'],

            'attachments'   => ['nullable', 'array', 'max:3'],
            'attachments.*' => [
                'file',
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,csv',
                'max:10240', // 10 MB / file
            ],

            'video_url' => ['nullable', 'url', 'max:500'],

            'target_mode'    => ['required', Rule::in(['role', 'individual', 'both'])],
            'target_roles'   => ['nullable', 'array'],
            'target_roles.*' => ['integer', 'exists:levels,id'],
            'target_users'   => ['nullable', 'array'],
            'target_users.*' => ['integer', 'exists:users,id'],
             'cover' => [
            'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp,gif',
            'max:4096', // 4 MB
        ],
        'remove_cover' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Tipe konten wajib dipilih.',
            'type.in'       => 'Tipe konten tidak valid.',

            'group_id.required' => 'Pengelompokan wajib dipilih.',
            'group_id.exists'   => 'Pengelompokan tidak ditemukan atau tidak aktif.',

            'title.required' => 'Judul wajib diisi.',
            'title.min'      => 'Judul minimal 3 karakter.',
            'title.max'      => 'Judul maksimal 180 karakter.',

            'description.max' => 'Deskripsi maksimal 8000 karakter.',

            'deadline_mode.required' => 'Mode waktu wajib dipilih.',
            'deadline_mode.in'       => 'Mode waktu tidak valid.',
            'deadline_at.date'       => 'Format deadline tidak valid.',

            'attachments.max' => 'Lampiran maksimal 3 file.',
            'attachments.*.mimes' => 'Format dokumen harus PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, atau CSV.',
            'attachments.*.max'   => 'Ukuran tiap file maksimal 10 MB.',

            'video_url.url' => 'URL video tidak valid.',
'cover.image' => 'File sampul harus berupa gambar.',
        'cover.mimes' => 'Format gambar sampul harus JPG, JPEG, PNG, WEBP, atau GIF.',
        'cover.max'   => 'Ukuran gambar sampul maksimal 4 MB.',
            'target_mode.required' => 'Mode target penerima wajib dipilih.',
            'target_roles.*.exists' => 'Role penerima tidak valid.',
            'target_users.*.exists' => 'User penerima tidak valid.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Pastikan deadline_at = null kalau mode unlimited
        if ($this->input('deadline_mode') === 'unlimited') {
            $this->merge(['deadline_at' => null]);
        }

        // Konversi string kosong ke null
        if ($this->input('deadline_at') === '') {
            $this->merge(['deadline_at' => null]);
        }
    }
}
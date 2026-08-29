<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'tipe' => ['required', Rule::in(['income', 'expense'])],
            'warna' => ['nullable', 'string', 'max:50'],
            'icon' => ['nullable', 'string', 'max:50'],
        ];
    }
}

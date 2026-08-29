<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountRequest extends FormRequest
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
            'nama_akun' => ['required', 'string', 'max:255'],
            'tipe' => ['required', Rule::in(['bank', 'e-wallet', 'kas', 'kartu_kredit'])],
            'saldo' => ['nullable', 'numeric', function ($attribute, $value, $fail) {
                if ($value !== null && $this->input('tipe') !== 'kartu_kredit' && $value < 0) {
                    $fail('Saldo awal tidak boleh negatif untuk tipe akun ini.');
                }
            }],
            'warna' => ['nullable', 'string', 'max:50'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

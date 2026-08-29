<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionRequest extends FormRequest
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
        $userId = $this->user()->id;
        $tipe = $this->input('tipe');

        return [
            'tipe' => ['required', Rule::in(['income', 'expense', 'transfer'])],
            'account_id' => [
                'required',
                'integer',
                Rule::exists('accounts', 'id')->where('user_id', $userId),
            ],
            'destination_account_id' => [
                'nullable',
                Rule::requiredIf($tipe === 'transfer'),
                'different:account_id',
                Rule::exists('accounts', 'id')->where('user_id', $userId),
            ],
            'category_id' => [
                'nullable',
                Rule::requiredIf(in_array($tipe, ['income', 'expense'], true)),
                Rule::exists('categories', 'id')->where(function ($query) use ($userId, $tipe) {
                    $query->where('user_id', $userId);
                    if (in_array($tipe, ['income', 'expense'], true)) {
                        $query->where('tipe', $tipe);
                    }
                }),
            ],
            'jumlah' => ['required', 'numeric', 'min:0.01', 'max:999999999999.99'],
            'tanggal' => ['required', 'date'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'account_id.exists' => 'Akun yang dipilih tidak valid atau bukan milik Anda.',
            'destination_account_id.exists' => 'Akun tujuan transfer tidak valid atau bukan milik Anda.',
            'destination_account_id.different' => 'Akun tujuan transfer harus berbeda dengan akun asal.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid atau tipe kategori tidak sesuai.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BudgetRequest extends FormRequest
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
        $budgetId = $this->route('budget')?->id;

        return [
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where(function ($query) use ($userId) {
                    $query->where('user_id', $userId)
                        ->where('tipe', 'expense');
                }),
            ],
            'limit_bulanan' => ['required', 'numeric', 'min:0.01', 'max:999999999999.99'],
            'periode' => [
                'required',
                'string',
                'regex:/^\d{4}-\d{2}$/',
                Rule::unique('budgets')->where(function ($query) {
                    return $query->where('category_id', $this->input('category_id'));
                })->ignore($budgetId),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'category_id.exists' => 'Kategori yang dipilih harus bertipe pengeluaran (expense) dan milik Anda.',
            'periode.regex' => 'Format periode harus YYYY-MM (contoh: 2026-08).',
            'periode.unique' => 'Budget untuk kategori ini pada periode tersebut sudah dibuat.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoalRequest extends FormRequest
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
            'nama_goal' => ['required', 'string', 'max:255'],
            'target' => ['required', 'numeric', 'min:1'],
            'deadline' => ['nullable', 'date'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ];
    }
}

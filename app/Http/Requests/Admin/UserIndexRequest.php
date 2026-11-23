<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UserIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['nullable', 'in:pending,approved,rejected,all'],
            'role' => ['nullable', 'in:customer,driver'],
            'search' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function statusFilter(): string
    {
        return $this->validated('status') ?? 'pending';
    }
}

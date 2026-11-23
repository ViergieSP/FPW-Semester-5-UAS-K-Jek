<?php

namespace App\Http\Requests\Admin;

use App\Models\FareSetting;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FareSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var FareSetting|null $fareSetting */
        $fareSetting = $this->route('fare_setting');

        return [
            'setting_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('fare_settings', 'setting_name')->ignore($fareSetting?->getKey()),
            ],
            'base_fare' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'fare_per_km' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'min_distance_km' => ['required', 'numeric', 'min:0', 'max:99.99'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'setting_name' => 'nama tarif',
            'base_fare' => 'tarif dasar',
            'fare_per_km' => 'tarif per km',
            'min_distance_km' => 'jarak minimum',
            'is_active' => 'status aktif',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'setting_name.unique' => 'Nama tarif sudah digunakan.',
        ];
    }
}

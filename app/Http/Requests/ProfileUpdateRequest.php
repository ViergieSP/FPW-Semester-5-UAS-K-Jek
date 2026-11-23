<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        $isDriver = $user instanceof User && $user->isDriver();
        $hasActiveOrder = $isDriver && $user->hasActiveOrder();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user instanceof User ? $user->id : null),
            ],
        ];

        if ($isDriver && !$hasActiveOrder) {
            $rules['vehicle_name'] = ['required', 'string', 'min:3', 'max:100'];
            $rules['license_plate'] = [
                'required',
                'string',
                'min:3',
                'max:20',
                'regex:/^[A-Za-z]{1,2}\s*\d{1,4}\s*[A-Za-z]{1,3}$/',
            ];
        }

        return $rules;
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $user = $this->user();

                if (!$user instanceof User || !$user->isDriver() || !$user->hasActiveOrder()) {
                    return;
                }

                if ($this->has('vehicle_name')) {
                    $submittedVehicle = Str::squish($this->string('vehicle_name')->toString());

                    if ($submittedVehicle !== (string) $user->vehicle_name) {
                        $validator->errors()->add(
                            'vehicle_name',
                            'Nama kendaraan tidak dapat diubah selama Anda masih memiliki pesanan aktif.'
                        );
                    }
                }

                if ($this->has('license_plate')) {
                    $submittedPlate = User::normalizeLicensePlate($this->string('license_plate')->toString());

                    if ($submittedPlate !== (string) $user->license_plate) {
                        $validator->errors()->add(
                            'license_plate',
                            'Plat nomor tidak dapat diubah selama Anda masih memiliki pesanan aktif.'
                        );
                    }
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'vehicle_name.required' => 'Nama kendaraan wajib diisi untuk driver.',
            'license_plate.required' => 'Plat nomor wajib diisi untuk driver.',
            'license_plate.regex' => 'Format plat nomor tidak valid (contoh: T 1234 AB).',
        ];
    }
}
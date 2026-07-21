<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateEmployeePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ];

        // Only enforce current password validation if one is already set
        if ($this->user()->password) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        return $rules;
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCitizenProfileRequest extends FormRequest
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
        $userId = $this->user()->id;

        return [
            'edit_name' => 'required|string|max:255',
            'edit_national_no' => ['nullable', Rule::unique('users', 'national_no')->ignore($userId), 'size:10'],
            'edit_email' => ['required', 'string', 'email', Rule::unique('users', 'email')->ignore($userId), 'max:255'],
            'edit_phone' => ['nullable', 'string', 'regex:/^\+?[0-9]{8,15}$/', Rule::unique('users', 'phone')->ignore($userId)],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'edit_name' => 'name',
            'edit_email' => 'email',
            'edit_national_no' => 'national number',
            'edit_phone' => 'phone number',
        ];
    }
}

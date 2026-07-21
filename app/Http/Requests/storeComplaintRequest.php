<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Complaint;

class storeComplaintRequest extends FormRequest
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
            'title' => [
                'required',
                'string',
                'max:255',
                function($attribute, $value, $fail) {
                    $duplicateExists = Complaint::where('title', $value)
                        ->where('category_id', $this->input('category_id'))
                        ->whereIn('status', ['pending', 'in_progress'])
                        ->exists();

                    if($duplicateExists){
                        $fail('Similar active complaint has been found. Please use a more specific title.');
                    }
                }
            ],
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,heic,heif|max:20480'
        ];

        // Apply guest-specific rules if not logged in
        if (!auth()->check()) {
            $rules['complainant_name'] = 'required|string|max:255';
            $rules['guest_national_no'] = 'required_without:passport_no|nullable|string|max:20';
            $rules['passport_no'] = 'required_without:guest_national_no|nullable|string|max:20';
            $rules['email'] = 'required|email|max:255';
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'complainant_name' => 'Full Name',
            'title' => 'Title',
            'email' => 'Email Address',
            'guest_national_no' => 'National Number',
            'passport_no' => 'Passport Number',
            'description' => 'Description'
        ];
    }
}

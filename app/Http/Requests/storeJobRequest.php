<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class storeJobRequest extends FormRequest
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
        return [
            'supervisor_ids' => 'required|array|min:1|max:1',
            'supervisor_ids.*' => 'exists:employees,id',
            'worker_ids' => 'required|array|min:1',
            'worker_ids.*' => 'exists:employees,id'
        ];
    }

    public function messages(): array{
        return [
            'supervisor_ids.required' => 'You must select at least one supervisor',
            'supervisor_ids.*.exists' => 'One of the selected supervisors does not exist in our records.',
            'worker_ids.required' => 'You must select at least one worker',
            'worker_ids.min' => 'Please assign at least one worker to this job.'
        ];
    }
}

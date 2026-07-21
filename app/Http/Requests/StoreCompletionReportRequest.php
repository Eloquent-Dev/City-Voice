<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompletionReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $jobOrder = $this->route('jobOrder');
        $employeeId = $this->user()->employee->id ?? null;

        // Automatically aborts with 403 if this returns false
        return $employeeId && $jobOrder->workers->contains($employeeId);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'supervisor_comments' => 'required|string|min:10|max:1000',
            'completion_image' => 'nullable|image|mimes:jpeg,png,jpg|max:20480',
            'accountability_check' => 'accepted'
        ];
    }
}

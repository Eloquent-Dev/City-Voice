<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkerStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $employee = $this->user()->employee;
        $jobOrder = $this->route('jobOrder');

        // Aborts with a 403 automatically if false
        return $employee && $jobOrder->workers->contains($employee->id);
    }

    public function rules(): array
    {
        return [
            'worker_status' => 'required|in:on_site,off_duty,in_route,off_site'
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\JobOrder;
use App\Http\Requests\StoreCompletionReportRequest;
use App\Services\SupervisorService;

class SupervisorController extends Controller
{
    public function createCompletionReport(JobOrder $jobOrder, SupervisorService $service)
    {
        $employeeId = auth()->user()->employee->id;

        if (!$service->isAuthorizedToManage($jobOrder, $employeeId)) {
            abort(403, 'You are not assigned to manage this Job Order.');
        }

        if ($jobOrder->status === 'resolved') {
            return redirect()->route('worker.assignments')
                ->with('error', 'This job order is already resolved');
        }

        return view('supervisor.completion.create', compact('jobOrder'));
    }

    public function storeCompletionReport(StoreCompletionReportRequest $request, JobOrder $jobOrder, SupervisorService $service)
    {
        // Authorization is automatically handled by StoreCompletionReportRequest

        $service->submitCompletionReport(
            $jobOrder,
            $request->validated(),
            $request->file('completion_image'),
            $request->user()->employee->id
        );

        return redirect()->route('worker.assignments')
            ->with('success', 'Completion Report submitted! The job is now under review by administration.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\JobOrder;
use App\Http\Requests\UpdateWorkerStatusRequest;
use App\Services\WorkerService;

class WorkerController extends Controller
{
    public function assignments()
    {
        $user = auth()->user();

        if (!$user->employee) {
            abort(403, 'Your account isn\'t linked to an employee. Contact the administrator.');
        }

        $assignments = $user->employee->assignedJobOrders()
            ->with(['complaint.category'])
            ->where('job_orders.status', 'in_progress')
            ->orderByRaw("
                CASE priority
                WHEN 'high' THEN 1
                WHEN 'medium' THEN 2
                WHEN 'low' THEN 3
                ELSE 4
                END ASC")
            ->latest('job_orders.created_at')
            ->paginate(10);

        return view('worker.assignments', compact('assignments'));
    }

    public function updateStatus(UpdateWorkerStatusRequest $request, JobOrder $jobOrder, WorkerService $service)
    {
        // Authorization is automatically handled by the Request class
        $service->updateWorkerStatus(
            $jobOrder,
            $request->user()->employee->id,
            $request->validated('worker_status')
        );

        return back();
    }

    public function toggleDuty(WorkerService $service)
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            abort(403, 'Unauthorized access');
        }

        $service->toggleDutyStatus($employee);

        return back();
    }

    public function show(JobOrder $jobOrder)
    {
        $employee = auth()->user()->employee;

        if (!$employee || !$jobOrder->workers->contains($employee->id) || $jobOrder->status !== 'in_progress') {
            abort(403, 'Unauthorized access');
        }

        $jobOrder->load(['complaint.category', 'workers.user']);

        return view('worker.show', compact('jobOrder', 'employee'));
    }
}

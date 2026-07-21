<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\JobOrder;
use Illuminate\Support\Facades\DB;

class WorkerService
{
    /**
     * Updates the worker's status and ensures only one job is active at a time.
     */
    public function updateWorkerStatus(JobOrder $jobOrder, int $employeeId, string $status): void
    {
        if (in_array($status, ['on_site', 'in_route'])) {
            DB::table('employee_job_order')
                ->where('employee_id', $employeeId)
                ->where('job_order_id', '!=', $jobOrder->id)
                ->whereIn('worker_status', ['on_site', 'in_route'])
                ->update(['worker_status' => 'off_site']);
        }

        $jobOrder->workers()->updateExistingPivot($employeeId, [
            'worker_status' => $status
        ]);
    }

    /**
     * Toggles the employee's duty status and safely handles clocking out.
     */
    public function toggleDutyStatus(Employee $employee): void
    {
        $newStatus = $employee->duty_status === 'on_duty' ? 'off_duty' : 'on_duty';

        $employee->update([
            'duty_status' => $newStatus
        ]);

        if ($newStatus === 'off_duty') {
            // Optimized: Single query to drop all active statuses for this employee
            DB::table('employee_job_order')
                ->where('employee_id', $employee->id)
                ->whereIn('worker_status', ['on_site', 'in_route'])
                ->update(['worker_status' => 'off_site']);
        }
    }
}

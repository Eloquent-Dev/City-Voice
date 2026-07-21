<?php

namespace App\Services;

use App\Models\JobOrder;
use App\Models\Employee;
use App\Notifications\complaintStatusUpdated;
use App\Notifications\jobOrderAssigned;

class DispatcherService
{
    /**
     * Retrieves and categorizes available on-duty crew members for a division.
     */
    public function getAvailableCrew(int $divisionId): array
    {
        $onDutyEmployees = Employee::with('user')
            ->where('division_id', $divisionId)
            ->where('duty_status', 'on_duty')
            ->get();

        return [
            'supervisors' => $onDutyEmployees->where('user.role', 'supervisor'),
            'workers' => $onDutyEmployees->where('user.role', 'worker')
        ];
    }

    /**
     * Assigns the team, updates statuses, and dispatches notifications.
     */
    public function dispatchTeam(JobOrder $jobOrder, array $validatedData, ?int $dispatcherEmployeeId): void
    {
        $teamIds = array_merge($validatedData['supervisor_ids'], $validatedData['worker_ids']);

        // Assign the crew
        $jobOrder->workers()->sync($teamIds);

        // Update Job Order
        $jobOrder->update([
            'status' => 'in_progress',
            'assigned_at' => now(),
            'assigned_by' => $dispatcherEmployeeId
        ]);

        // Update Complaint
        $jobOrder->complaint->update([
            'status' => 'in_progress'
        ]);

        // Notify Citizen
        if ($jobOrder->complaint->user) {
            $jobOrder->complaint->user->notify(new complaintStatusUpdated($jobOrder->complaint));
        }

        // Notify assigned crew
        $jobOrder->load('workers.user');
        foreach ($jobOrder->workers as $employee) {
            if ($employee->user) {
                $employee->user->notify(new jobOrderAssigned($jobOrder->complaint));
            }
        }
    }

    /**
     * Toggles the urgency flag on a job order.
     */
    public function toggleUrgency(JobOrder $jobOrder, bool $isUrgent): void
    {
        $jobOrder->update(['is_urgent' => $isUrgent]);
    }
}

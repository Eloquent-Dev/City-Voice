<?php

namespace App\Services;

use App\Models\JobOrder;
use App\Models\CompletionReport;
use App\Models\User;
use App\Notifications\complaintStatusUpdated;
use App\Notifications\completionReportSubmitted;
use Illuminate\Support\Facades\Notification;

class SupervisorService
{
    /**
     * Verifies if an employee is assigned to manage the job order.
     */
    public function isAuthorizedToManage(JobOrder $jobOrder, int $employeeId): bool
    {
        return $jobOrder->workers->contains($employeeId);
    }

    /**
     * Processes the completion report, updates statuses, and triggers notifications.
     */
    public function submitCompletionReport(JobOrder $jobOrder, array $validatedData, $imageFile, int $employeeId): void
    {
        $imagePath = $imageFile ? $imageFile->store('completions', 'public') : null;

        // 1. Create the Completion Report
        CompletionReport::create([
            'image_path' => $imagePath,
            'job_order_id' => $jobOrder->id,
            'reported_by' => $employeeId,
            'supervisor_comments' => $validatedData['supervisor_comments'],
            'started_at' => $jobOrder->created_at,
            'completed_at' => now(),
        ]);

        // 2. Update Job Order
        $jobOrder->update([
            'status' => 'under_review',
            'supervisor_comments' => $validatedData['supervisor_comments'],
            'completed_at' => now()
        ]);

        // 3. Update Complaint Status & Notify Citizen
        $jobOrder->complaint->update([
            'status' => 'under_review'
        ]);
        $jobOrder->complaint->user->notify(new complaintStatusUpdated($jobOrder->complaint));

        // 4. Update the workers' pivot table status
        $jobOrder->workers()->updateExistingPivot($jobOrder->workers->pluck('id'), [
            'worker_status' => 'off_site'
        ]);

        // 5. Notify Administration
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new completionReportSubmitted($jobOrder->complaint));
    }
}

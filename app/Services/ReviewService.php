<?php

namespace App\Services;

use App\Models\JobOrder;
use App\Notifications\complaintStatusUpdated;

class ReviewService
{
    /**
     * Processes the review decision and returns a success message.
     */
    public function processDecision(JobOrder $jobOrder, array $validatedData, int $adminId): string
    {
        $decision = $validatedData['decision'];
        $notes = $validatedData['admin_notes'] ?? null;
        $message = '';

        switch ($decision) {
            case 'approve':
                $jobOrder->update(['status' => 'completed']);
                $jobOrder->complaint->update([
                    'status' => 'approved',
                    'approved_at' => now(),
                    'approved_by' => $adminId
                ]);
                $message = 'Work approved! Ticket is now awaiting citizen feedback.';
                break;

            case 'reject_to_crew':
                $jobOrder->update(['status' => 'in_progress']);
                $jobOrder->complaint->update(['status' => 'in_progress', 'rejection_reason' => $notes]);

                if ($jobOrder->completionReport) {
                    $jobOrder->completionReport->delete();
                }
                $message = 'Ticket sent back to the assigned crew.';
                break;

            case 'reject_to_dispatcher':
                $jobOrder->update(['status' => 'pending']);
                $jobOrder->complaint->update(['status' => 'pending', 'rejection_reason' => $notes]);

                $jobOrder->workers()->detach();

                if ($jobOrder->completionReport) {
                    $jobOrder->completionReport->delete();
                }
                $message = 'Ticket returned to Dispatcher for reassignment.';
                break;

            case 'reject_complaint':
                $jobOrder->update(['status' => 'rejected']);
                $jobOrder->complaint->update([
                    'status' => 'rejected',
                    'rejected_at' => now(),
                    'rejected_by' => $adminId,
                    'rejection_reason' => $notes
                ]);
                $message = 'Complaint has been officially rejected & closed.';
                break;
        }

        // Notify the user of the status change
        $jobOrder->complaint->user->notify(new complaintStatusUpdated($jobOrder->complaint));

        return $message;
    }
}

<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\User;
use App\Notifications\newComplaintSubmitted;
use App\Notifications\complaintStatusUpdated;
use Illuminate\Support\Facades\Notification;

class FeedbackService
{
    /**
     * Processes the citizen's feedback and handles automatic escalations.
     */
    public function processFeedback(Complaint $complaint, array $validatedData): void
    {
        // 1. Store the feedback
        $complaint->feedback()->create($validatedData);

        // 2. Handle Poor Rating Escalation (<= 2.5)
        if ($validatedData['rating'] <= 2.5) {

            // Soft-delete and reopen the original complaint
            $complaint->update([
                'status' => 'reopened',
                'deleted_at' => now()
            ]);

            $complaint->user->notify(new complaintStatusUpdated($complaint));

            // Generate the automated follow-up ticket
            $newComplaint = Complaint::create([
                'title' => 'Follow-up: ' . $complaint->title,
                'latitude' => $complaint->latitude,
                'longitude' => $complaint->longitude,
                'description' => $validatedData['quality_comments'] ?? 'No additional comments',
                'category_id' => $complaint->category_id,
                'user_id' => $complaint->user_id,
                'reopened_from_id' => $complaint->id
            ]);

            // Alert the dispatchers
            $dispatchers = User::where('role', 'dispatcher')->get();
            Notification::send($dispatchers, new newComplaintSubmitted($newComplaint));
        }
        // 3. Handle Positive/Acceptable Rating (> 2.5)
        else {
            $complaint->update([
                'status' => 'resolved',
                'resolved_at' => now(),
            ]);

            $complaint->user->notify(new complaintStatusUpdated($complaint));
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Complaint;
use App\Notifications\newComplaintSubmitted;
use Illuminate\Http\Request;
use App\Notifications\complaintStatusUpdated;
use App\Models\User;

class FeedbackController extends Controller
{
    public function store(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'rating' => 'required|numeric|min:0|max:5',
            'quality_comments' => 'nullable|string',
            'speed_rating' => 'required|numeric|min:0|max:5',
        ]);

       $complaint->feedback()->create($validated);

        if($validated['rating']<=2.5){
            $complaint->update(['status' => 'reopened', 'deleted_at' => now()]);

            $complaint->user->notify(new complaintStatusUpdated($complaint));

            $newComplaint = Complaint::create([
                'title'=>'Follow-up: '.$complaint->title,
                'latitude'=>$complaint->latitude,
                'longitude'=>$complaint->longitude,
                'description'=>$complaint->feedback->quality_comments ?? 'No additional comments',
                'category_id'=>$complaint->category_id,
                'user_id'=>$complaint->user_id,
                'reopened_from_id'=>$complaint->id
            ]);

            $dispatchers = User::where('role','dispatcher')->get();
            foreach($dispatchers as $dispatcher){
                $dispatcher->notify(new newComplaintSubmitted($newComplaint));
            }
        }
        else{
            $complaint->update([
                'status' => 'resolved',
                'resolved_at' => now(),
                ]);

            $complaint->user->notify(new complaintStatusUpdated($complaint));
        }

        return redirect()->route('complaints.index')->with('success', 'Feedback submitted successfully.');
    }
}

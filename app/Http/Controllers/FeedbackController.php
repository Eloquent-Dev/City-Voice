<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Http\Requests\StoreFeedbackRequest;
use App\Services\FeedbackService;

class FeedbackController extends Controller
{
    public function store(StoreFeedbackRequest $request, Complaint $complaint, FeedbackService $service)
    {
        $service->processFeedback($complaint, $request->validated());

        return redirect()->route('complaints.index')
            ->with('success', 'Feedback submitted successfully.');
    }
}

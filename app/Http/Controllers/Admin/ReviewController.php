<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobOrder;
use App\Http\Requests\ProcessReviewRequest;
use App\Services\ReviewService;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class ReviewController extends Controller
{
    public function index()
    {
        $pendingReviews = JobOrder::with(['complaint.category', 'completionReport', 'workers.user'])
            ->where('status', 'under_review')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.reviews.index', compact('pendingReviews'));
    }

    public function show(JobOrder $jobOrder)
    {
        if ($jobOrder->status !== 'under_review') {
            return redirect()->route('admin.reviews.index')
                ->with('error', 'This job order is not currently under review.');
        }

        $jobOrder->load(['complaint.category', 'completionReport', 'workers.user']);

        return view('admin.reviews.show', compact('jobOrder'));
    }

    public function process(JobOrder $jobOrder, ProcessReviewRequest $request, ReviewService $service)
    {
        // 1. Guard clause for invalid state
        if ($jobOrder->status !== 'under_review') {
            return back()->with('error', 'Invalid action.');
        }

        // 2. Delegate the complex logic to the service
        $message = $service->processDecision($jobOrder, $request->validated(), auth()->id());

        // 3. Return the response
        return redirect()->route('admin.reviews.index')->with('success', $message);
    }

    public function exportPDF(JobOrder $jobOrder)
    {
        $jobOrder->load(['complaint.category', 'complaint.user', 'workers.user', 'completionReport']);

        $pdf = PDF::loadView('admin.reviews.pdf', compact('jobOrder'));

        $filename = 'QA_Review_Report_#' . $jobOrder->id . '_' . now()->format('Y_m_d') . '.pdf';

        return $pdf->download($filename);
    }
}

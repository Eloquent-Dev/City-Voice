<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobOrder;
use Illuminate\Http\Request;
use App\Notifications\complaintStatusUpdated;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;

class ReviewController extends Controller
{
    public function index(){
        $pendingReviews = JobOrder::with(['complaint.category','completionReport','workers.user'])
        ->where('status','under_review')
        ->orderBy('updated_at','desc')
        ->get();

        return view('admin.reviews.index',compact('pendingReviews'));
    }

    public function show(JobOrder $jobOrder){
        if($jobOrder->status !== 'under_review'){
            return redirect()->route('admin.reviews.index')->with('error','This job order is not currently under review.');
        }

        $jobOrder->load(['complaint.category','completionReport','workers.user']);

        return view('admin.reviews.show',compact('jobOrder'));
    }

    public function process(JobOrder $jobOrder, Request $request){
        if($jobOrder->status !== 'under_review'){
            return back()->with('error','Invalid action.');
        }

        $validated = $request->validate([
            'decision' => 'required|in:approve,reject_to_crew,reject_to_dispatcher,reject_complaint',
            'admin_notes' => 'required_unless:decision,approve,reject_to_crew,reject_to_dispatcher|nullable|string|max:1000'
        ]);

        $decision = $validated['decision'];
        $notes = $validated['admin_notes'];

        switch($decision){
            case 'approve':

                $jobOrder->update(['status'=>'completed']);
                $jobOrder->complaint->update([
                    'status' => 'approved',
                    'approved_at' => now(),
                    'approved_by' => auth()->id()
                ]);
                $message = 'Work approved! ticket is now awaiting citizen feedback.';
                break;

            case 'reject_to_crew':

                $jobOrder->update(['status'=>'in_progress','return_reason' => $notes]);
                $jobOrder->complaint->update(['status' => 'in_progress']);

                if($jobOrder->completionReport){
                    $jobOrder->completionReport->delete();
                }

                $message = 'Ticket sent back to the assigned crew.';
                break;

            case 'reject_to_dispatcher' :

                $jobOrder->update(['status'=>'pending']);
                $jobOrder->complaint->update(['status'=>'pending']);

                $jobOrder->workers()->detach();

                if($jobOrder->completionReport){
                    $jobOrder->completionReport->delete();
                }
                $message = 'Ticket returned to Dispatcher for reassignment.';
                break;

            case 'reject_complaint' :

                $jobOrder->update(['status' => 'rejected']);
                $jobOrder->complaint->update([
                    'status' => 'rejected',
                    'rejected_at' => now(),
                    'rejected_by' => auth()->id(),
                    'rejection_reason' => $notes
                ]);

                $message = 'Complaint has been officially rejected & closed.';
                break;
        }
                $jobOrder->complaint->user->notify(new complaintStatusUpdated($jobOrder->complaint));

        return redirect()->route('admin.reviews.index')->with('success',$message);
    }

    public function exportPDF(JobOrder $jobOrder){

        $jobOrder->load(['complaint.category','complaint.user','workers.user','completionReport']);

        $pdf = PDF::loadView('admin.reviews.pdf', compact('jobOrder'));

        $filename = 'QA_Review_Report_#'. $jobOrder->id . '_' . now()->format('Y_m_d') . '.pdf';

        return $pdf->download($filename);
    }
}

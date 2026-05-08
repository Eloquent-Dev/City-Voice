<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminReport;
use Illuminate\Http\Request;
use App\Models\Complaint;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(){
        $reports = AdminReport::with('generator')->latest()->paginate(10);
        return view('admin.reports.index', compact('reports'));
    }

    public function generate(){
        $latestReport = AdminReport::latest()->first();

        if($latestReport && $latestReport->created_at->addDays(30)->isFuture()){
            $daysLeft = now()->diffInDays($latestReport->created_at->addDays(30));

            $displayDays = $daysLeft > 0 ? $daysLeft : 1;

            return back()->with('error',"Global Cooldown Active: Please wait {$displayDays} more day(s) before generating a new report.");
        }

        $startDate = now()->subDays(30);

        $endDate = now();

        $complaints = Complaint::WhereBetween('created_at',[$startDate,$endDate])->get();

        $totalRecieved = $complaints->count();
        $totalResolved = $complaints->where('status','resolved')->count();
        $totalApproved = $complaints->where('status','approved')->count();
        $totalPending = $complaints->where('status','pending')->count();
        $totalInProgress = $complaints->where('status','in_progress')->count();
        $totalUnderReview = $complaints->where('status','under_review')->count();
        $totalReopened = $complaints->where('status','reopened')->count();
        $totalRejected = $complaints->where('status','rejected')->count();

        $approvedComplaints = $complaints->where('status','approved');
        $totalHours = 0;

        foreach($approvedComplaints as $complaint){
            $completionTime = $complaint->approved_at ? Carbon::parse($complaint->approved_at) : $complaint->updated_at;
            $totalHours += $complaint->created_at->diffInHours($completionTime);
        }

        $avgResolutionTime = $approvedComplaints->count() > 0
        ? round($totalHours / $approvedComplaints->count(), 1)
        : 0;

        $metrics = [
            'period' => $startDate->format('M d, Y'). ' - ' . $endDate->format('M d, Y'),
            'total_received' => $totalRecieved,
            'total_resolved' => $totalResolved,
            'total_approved' => $totalApproved,
            'total_pending' => $totalPending,
            'total_in_progress' => $totalInProgress,
            'total_under_review' => $totalUnderReview,
            'total_reopened' => $totalReopened,
            'total_rejected' => $totalRejected,
            'resolution_rate' => $totalRecieved > 0 ? round(($totalResolved / $totalRecieved) * 100, 1) : 0,
            'avg_resolution_hours' => $avgResolutionTime,
            'total_hours' => $totalHours,
        ];

        $report = AdminReport::create([
            'title' => 'System KPI Snapshot - '. now()->format('M d, Y'),
            'metrics' => $metrics,
            'generated_by' => auth()->user()->employee->id
        ]);

        return redirect()->route('admin.reports.index')->with('success','New KPI Report generated successfully!');
    }

    public function show(AdminReport $report){
        return view('admin.reports.show', compact('report'));
    }

    public function exportPDF(AdminReport $report){

        $pdf = Pdf::loadView('admin.reports.pdf', compact('report'));

        $filename = 'KPI_Report_' . $report->created_at->format('Y_m_d') . '.pdf';

        return $pdf->download($filename);
    }
}

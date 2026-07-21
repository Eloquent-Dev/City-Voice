<?php

namespace App\Services;

use App\Models\AdminReport;
use App\Models\Complaint;
use Carbon\Carbon;

class ReportService
{
    /**
     * Checks the click-driven 30-day cooldown.
     * Returns days left, or null if generation is allowed.
     */
    public function getCooldownDaysLeft(): ?int
    {
        $latestReport = AdminReport::latest()->first();

        if ($latestReport && $latestReport->created_at->addDays(30)->isFuture()) {
            $daysLeft = now()->diffInDays($latestReport->created_at->addDays(30));
            return $daysLeft > 0 ? $daysLeft : 1;
        }

        return null;
    }

    /**
     * Crunches the complaint data and creates the report.
     */
    public function generateMonthlyKPIReport(int $employeeId): AdminReport
    {
        $startDate = now()->subDays(30);
        $endDate = now();

        $complaints = Complaint::whereBetween('created_at', [$startDate, $endDate])->get();

        $totalReceived = $complaints->count();
        $totalResolved = $complaints->where('status', 'resolved')->count();

        $approvedComplaints = $complaints->where('status', 'approved');
        $totalHours = 0;

        foreach ($approvedComplaints as $complaint) {
            $completionTime = $complaint->approved_at ? Carbon::parse($complaint->approved_at) : $complaint->updated_at;
            $totalHours += $complaint->created_at->diffInHours($completionTime);
        }

        $avgResolutionTime = $approvedComplaints->count() > 0
            ? round($totalHours / $approvedComplaints->count(), 1)
            : 0;

        $metrics = [
            'period'               => $startDate->format('M d, Y') . ' - ' . $endDate->format('M d, Y'),
            'total_received'       => $totalReceived,
            'total_resolved'       => $totalResolved,
            'total_approved'       => $approvedComplaints->count(),
            'total_pending'        => $complaints->where('status', 'pending')->count(),
            'total_in_progress'    => $complaints->where('status', 'in_progress')->count(),
            'total_under_review'   => $complaints->where('status', 'under_review')->count(),
            'total_reopened'       => $complaints->where('status', 'reopened')->count(),
            'total_rejected'       => $complaints->where('status', 'rejected')->count(),
            'resolution_rate'      => $totalReceived > 0 ? round(($totalResolved / $totalReceived) * 100, 1) : 0,
            'avg_resolution_hours' => $avgResolutionTime,
            'total_hours'          => $totalHours,
        ];

        return AdminReport::create([
            'title'        => 'System KPI Snapshot - ' . now()->format('M d, Y'),
            'metrics'      => $metrics,
            'generated_by' => $employeeId
        ]);
    }
}

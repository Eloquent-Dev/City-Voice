<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminReport;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        $reports = AdminReport::with('generator')->latest()->paginate(10);

        return view('admin.reports.index', compact('reports'));
    }

    public function generate(ReportService $service)
    {
        if ($daysLeft = $service->getCooldownDaysLeft()) {
            return back()->with('error', "Global Cooldown Active: Please wait {$daysLeft} more day(s) before generating a new report.");
        }

        $service->generateMonthlyKPIReport(auth()->user()->employee->id);

        return redirect()->route('admin.reports.index')
            ->with('success', 'New KPI Report generated successfully!');
    }

    public function show(AdminReport $report)
    {
        return view('admin.reports.show', compact('report'));
    }

    public function exportPDF(AdminReport $report)
    {
        $pdf = Pdf::loadView('admin.reports.pdf', compact('report'));

        $filename = 'KPI_Report_' . $report->created_at->format('Y_m_d') . '.pdf';

        return $pdf->download($filename);
    }
}

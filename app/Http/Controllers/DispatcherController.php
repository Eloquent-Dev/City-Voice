<?php

namespace App\Http\Controllers;

use App\Models\JobOrder;
use App\Http\Requests\storeJobRequest;
use Illuminate\Http\Request;
use App\Services\DispatcherService;

class DispatcherController extends Controller
{
    public function index()
    {
        $jobOrders = JobOrder::with(['complaint.category', 'complaint.user', 'workers'])
            ->whereIn('status', ['pending', 'reopened', 'in_progress'])
            ->orderByDesc('is_urgent')
            ->orderByRaw("priority = 'high' DESC, priority = 'medium' DESC, priority = 'low' DESC")
            ->oldest()
            ->paginate(15);

        return view('dispatcher.job_orders.index', compact('jobOrders'));
    }

    public function show(JobOrder $jobOrder, DispatcherService $service)
    {
        if (!in_array($jobOrder->status, ['pending', 'in_progress'])) {
            abort(403, 'This Job Order has already been dispatched');
        }

        $jobOrder->load(['complaint.category', 'complaint.user', 'workers.user']);

        $crew = $service->getAvailableCrew($jobOrder->complaint->category->division_id);

        return view('dispatcher.job_orders.show', [
            'jobOrder' => $jobOrder,
            'supervisors' => $crew['supervisors'],
            'workers' => $crew['workers']
        ]);
    }

    public function update(storeJobRequest $request, JobOrder $jobOrder, DispatcherService $service)
{
    if (!in_array($jobOrder->status, ['pending', 'in_progress'])) {
        abort(403, 'This Job Order has already been dispatched');
    }

    // $request->validated() automatically uses your storeJobRequest rules!
    $service->dispatchTeam($jobOrder, $request->validated(), auth()->user()->employee->id ?? null);

    return redirect()->route('dispatcher.job_orders.index')
        ->with('success', 'Field team dispatched! Job Order is now In Progress.');
}

    public function updateUrgency(Request $request, JobOrder $jobOrder, DispatcherService $service)
    {
        $validated = $request->validate([
            'is_urgent' => 'required|boolean'
        ]);

        $service->toggleUrgency($jobOrder, $validated['is_urgent']);

        return response()->json([
            'success' => true,
            'message' => 'Urgency updated successfully.'
        ]);
    }
}

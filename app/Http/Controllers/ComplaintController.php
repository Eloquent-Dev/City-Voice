<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Complaint;
use App\Http\Requests\StoreComplaintRequest;
use App\Services\ComplaintService;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = Complaint::where('user_id', auth()->id())
            ->with('category')
            ->latest()
            ->get();

        return view('complaints.index', compact('complaints'));
    }

    public function show(Complaint $complaint)
    {
        if ($complaint->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action. You can only view your own complaints.');
        }

        return view('complaints.show', compact('complaint'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('complaints.create', compact('categories'));
    }

    public function store(StoreComplaintRequest $request, ComplaintService $service)
    {
        // The Request class automatically validates before hitting this line
        $service->processNewComplaint($request->validated(), $request->file('image'));

        return redirect()->route('home')->with('success', 'Your complaint has been submitted and is pending');
    }
}

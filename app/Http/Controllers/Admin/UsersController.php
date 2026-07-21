<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Division;
use App\Models\Category;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Http\Requests\UpdateUserDivisionRequest;
use App\Http\Requests\UpdateComplaintDetailsRequest;
use App\Services\AdminUserService;
use Exception;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('employee');

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm)
                  ->orWhere('national_no', 'like', $searchTerm);
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('created_at', 'asc')->paginate(15)->withQueryString();

        $stats = [
            'total' => User::count(),
            'admins' => User::where('role', 'admin')->count(),
            'dispatchers' => User::where('role', 'dispatcher')->count(),
            'supervisors' => User::where('role', 'supervisor')->count(),
            'workers' => User::where('role', 'worker')->count(),
            'citizens' => User::where('role', 'citizen')->count()
        ];

        $divisions = Division::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'stats', 'divisions'));
    }

    public function updateRole(UpdateUserRoleRequest $request, User $user, AdminUserService $service)
    {
        try {
            $message = $service->updateUserRole($user, $request->validated('role'), auth()->id());
            return back()->with('success', $message);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateDivision(UpdateUserDivisionRequest $request, User $user, AdminUserService $service)
    {
        try {
            $message = $service->updateUserDivision($user, $request->validated('division_id'));
            return redirect()->route('admin.users.index')->with('success', $message);
        } catch (Exception $e) {
            return redirect()->route('admin.users.index')->with('error', $e->getMessage());
        }
    }

    public function complaints(User $user)
    {
        $categories = $user->complaint()->exists() ? Category::all() : [];
        $complaints = $user->complaint()->orderBy('id', 'asc')->paginate(15);

        return view('admin.users.complaints.index', compact('user', 'complaints', 'categories'));
    }

    public function showComplaint(Complaint $complaint)
    {
        $complaint->load([
            'user', 'category', 'approvedBy.user', 'rejectedBy.user',
            'resolvedBy.user', 'jobOrders.assignedBy.user',
            'jobOrders.workers.user', 'jobOrders.completionReport.reportedBy.user',
            'feedback'
        ]);

        // Note: session()->put('complaint_id', $complaint->id) has been removed for API compatibility!

        return view('admin.users.complaints.show', compact('complaint'));
    }

    public function showProfile(User $user, Complaint $complaint)
    {
        $user->load('employee.division');

        return view('admin.users.profile.show', compact('user', 'complaint'));
    }

    public function updateDetails(UpdateComplaintDetailsRequest $request, Complaint $complaint, AdminUserService $service)
    {
        $service->updateComplaintDetails($complaint, $request->validated());

        return back()->with('success', 'Complaint Details Updated Successfully.');
    }

    public function destroy(User $user, AdminUserService $service)
    {
        try {
            $message = $service->deleteUser($user, auth()->id());
            return back()->with('success', $message);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

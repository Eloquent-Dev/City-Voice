<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateEmployeeProfileRequest;
use App\Http\Requests\UpdateEmployeePasswordRequest;
use App\Services\EmployeeProfileService;

class EmployeeProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = auth()->user();

        return view('profile.employee.show', compact('user'));
    }

    public function edit(Request $request)
    {
        $user = auth()->user();

        return view('profile.employee.edit', compact('user'));
    }

    public function update(UpdateEmployeeProfileRequest $request, EmployeeProfileService $service)
    {
        $service->updateProfile($request->user(), $request->validated());

        return redirect()->route('employee.profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(UpdateEmployeePasswordRequest $request, EmployeeProfileService $service)
    {
        $service->updatePassword($request->user(), $request->validated('new_password'));

        return redirect()->route('employee.profile.show')
            ->with('success', 'Password updated successfully.');
    }
}

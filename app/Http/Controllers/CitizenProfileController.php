<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCitizenProfileRequest;
use App\Http\Requests\UpdateCitizenPasswordRequest;
use App\Services\CitizenProfileService;

class CitizenProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        return view('profile.citizen.show', compact('user'));
    }

    public function edit()
    {
        $user = auth()->user();
        return view('profile.citizen.edit', compact('user'));
    }

    public function update(UpdateCitizenProfileRequest $request, CitizenProfileService $service)
    {
        $service->updateProfile($request->user(), $request->validated());

        return redirect()->route('citizen.profile.show')
            ->with('success', 'Your profile updated successfully.');
    }

    public function updatePassword(UpdateCitizenPasswordRequest $request, CitizenProfileService $service)
    {
        $service->updatePassword($request->user(), $request->validated('new_password'));

        return redirect()->route('citizen.profile.show')
            ->with('success', 'Your password updated successfully.');
    }
}

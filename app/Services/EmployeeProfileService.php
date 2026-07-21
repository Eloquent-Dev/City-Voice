<?php

namespace App\Services;

use App\Models\User;

class EmployeeProfileService
{
    /**
     * Updates the employee's core user profile and role-specific job title.
     */
    public function updateProfile(User $user, array $validatedData): void
    {
        // 1. Update core User details
        $user->update([
            'name' => $validatedData['edit_name'],
            'phone' => $validatedData['edit_phone'],
            'national_no' => $validatedData['edit_national_no'],
            'email' => $validatedData['edit_email'],
        ]);

        // 2. Handle role-based Job Title logic
        if ($user->role === 'admin') {
            $user->employee()->update([
                'job_title' => $validatedData['edit_job_title'],
            ]);
        } else {
            $user->employee()->update([
                'pending_job_title' => $validatedData['edit_job_title'],
            ]);
        }
    }

    /**
     * Secures and updates the employee's password.
     */
    public function updatePassword(User $user, string $newPassword): void
    {
        $user->update([
            'password' => bcrypt($newPassword),
        ]);
    }
}

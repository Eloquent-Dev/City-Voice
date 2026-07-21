<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CitizenProfileService
{
    /**
     * Maps and updates the citizen's profile details.
     */
    public function updateProfile(User $user, array $validatedData): void
    {
        $user->update([
            'name' => $validatedData['edit_name'],
            'national_no' => $validatedData['edit_national_no'],
            'email' => $validatedData['edit_email'],
            'phone' => $validatedData['edit_phone'],
        ]);
    }

    /**
     * Updates the citizen's password.
     */
    public function updatePassword(User $user, string $newPassword): void
    {
        $user->update([
            'password' => Hash::make($newPassword),
        ]);
    }
}

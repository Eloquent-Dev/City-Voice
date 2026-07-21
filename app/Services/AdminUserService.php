<?php

namespace App\Services;

use App\Models\User;
use App\Models\Employee;
use App\Models\Complaint;
use App\Notifications\complaintStatusUpdated;
use Exception;

class AdminUserService
{
    public function updateUserRole(User $user, string $newRole, int $currentAdminId): string
    {
        if ($user->id === $currentAdminId) {
            throw new Exception("Critical Error: You can't demote your own Admin account.");
        }

        $oldRole = $user->role;

        // Handle Employee record creation/deletion based on role transitions
        if ($oldRole === 'citizen' && $newRole !== 'citizen') {
            Employee::firstOrCreate(['user_id' => $user->id]);
        } elseif (in_array($oldRole, ['dispatcher', 'admin', 'supervisor', 'worker']) && $newRole === 'citizen') {
            $user->employee()->delete();
        }

        $user->update(['role' => $newRole]);

        return "{$user->name}'s role has been updated from {$oldRole} to {$newRole}.";
    }

    public function updateUserDivision(User $user, ?int $divisionId): string
    {
        if (!in_array($user->role, ['dispatcher', 'worker', 'supervisor', 'admin'])) {
            throw new Exception('Divisions can only be assigned to employees');
        }

        $user->employee()->update(['division_id' => $divisionId]);

        if ($divisionId) {
            return "{$user->name} has been successfully assigned to their new division.";
        }

        return "{$user->name} has been successfully unassigned from all divisions.";
    }

    public function updateComplaintDetails(Complaint $complaint, array $validatedData): void
    {
        $oldStatus = $complaint->status;

        $complaint->update([
            'category_id' => $validatedData['category_id'],
            'status' => $validatedData['status']
        ]);

        if ($oldStatus !== $validatedData['status']) {
            $complaint->user->notify(new complaintStatusUpdated($complaint));
        }
    }

    public function deleteUser(User $user, int $currentAdminId): string
    {
        if ($user->id === $currentAdminId) {
            throw new Exception("Critical Error: You can't delete your own Admin account.");
        }

        if ($user->role === 'admin') {
            throw new Exception("Security Error: You cannot delete another Admin account.");
        }

        if ($user->employee()->exists()) {
            if ($user->employee->assignedJobOrders()->count() > 0) {
                throw new Exception("Can't delete user: This employee has active or historical job orders assigned to them.");
            }
            $user->employee()->delete();
        }

        $userName = $user->name;
        $user->delete();

        return "User {$userName} has been permanently deleted from the system.";
    }
}

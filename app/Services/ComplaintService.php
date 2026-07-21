<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\User;
use App\Models\JobOrder;
use App\Notifications\newComplaintSubmitted;
use App\Notifications\ComplaintRecieved;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ComplaintService
{
    public function processNewComplaint(array $validatedData, $imageFile = null)
    {
        // 1. Handle User Resolution
        $userId = auth()->id();

        if (!auth()->check()) {
            $user = User::firstOrCreate(
                ['email' => $validatedData['email']],
                [
                    'name' => $validatedData['complainant_name'],
                    'national_no' => $validatedData['guest_national_no'] ?? null,
                    'passport_no' => $validatedData['passport_no'] ?? null,
                    'password' => Hash::make(Str::random(10)),
                ]
            );
            $userId = $user->id;
        }

        // 2. Handle Image Upload
        $imagePath = $imageFile ? $imageFile->store('complaints', 'public') : null;

        // 3. Proximity Check (Currently unused in creation, but preserved)
        $newLat = $validatedData['latitude'];
        $newLng = $validatedData['longitude'];

        $nearbyComplaints = Complaint::select('id', 'description')
            ->whereNull('parent_id')
            ->where('category_id', $validatedData['category_id'])
            ->where('created_at', '>=', now()->subHours(48))
            ->whereRaw("(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) < 0.5",
                [$newLat, $newLng, $newLat]
            )->get();

        // 4. Create the Complaint
        $complaint = Complaint::create([
            'title' => $validatedData['title'],
            'category_id' => $validatedData['category_id'],
            'description' => $validatedData['description'],
            'latitude' => $validatedData['latitude'],
            'longitude' => $validatedData['longitude'],
            'user_id' => $userId,
            'image_path' => $imagePath,
            'status' => 'pending'
        ]);

        // 5. Create Job Order
        JobOrder::create(['complaint_id' => $complaint->id]);

        // 6. Dispatch Notifications
        $dispatchers = User::where('role', 'dispatcher')->get();
        Notification::send($dispatchers, new newComplaintSubmitted($complaint));

        if (auth()->check()) {
            auth()->user()->notify(new ComplaintRecieved($complaint));
        }

        return $complaint;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function show()
    {
        return view('auth.profile');
    }

    public function update(ProfileUpdateRequest $request)
    {
        try {
            Log::info('Profile update request', $request->all());

            $user = auth()->user();

            // ✅ Get ONLY validated data
            $data = $request->validated();

            // ✅ Handle password
            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            // ✅ Handle profile photo
            if ($request->hasFile('profile_photo')) {

                // Delete old photo (optional but recommended)
                if ($user->profile_photo && \Storage::disk('public')->exists($user->profile_photo)) {
                    \Storage::disk('public')->delete($user->profile_photo);
                }

                $data['profile_photo'] = $request->file('profile_photo')
                    ->store('profile_photos', 'public');
            }

            // ✅ Update user
            $user->update($data);

            Log::info('Profile updated successfully', [
                'user_id' => $user->id
            ]);

            return redirect()->back()->with('success', 'Profile updated.');

        } catch (\Exception $e) {
            Log::error('Profile update failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Failed to update profile.');
        }
    }
}

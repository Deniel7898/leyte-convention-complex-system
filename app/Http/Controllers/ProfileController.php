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

            $data = $request->except(['password', 'password_confirmation']);

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            if ($request->hasFile('profile_photo')) {
                $data['profile_photo'] = $request->file('profile_photo')->store('profile_photos', 'public');
            }

            auth()->user()->update($data);

            Log::info('Profile updated successfully for user', ['user_id' => auth()->id()]);

            return redirect()->back()->with('success', 'Profile updated.');
        } catch (\Exception $e) {
            Log::error('Profile update failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to update profile. Please try again.');
        }
    }
}

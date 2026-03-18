<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderByRaw("CASE WHEN role = 'admin' THEN 1 ELSE 0 END")
            ->orderBy('created_at', 'desc')
            ->get();
        $users_table = view('users.table', compact('users'))->render();
        return view('users.index', compact('users_table'));
    }

    public function create()
    {
        return view('users.form');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'first_name' => 'nullable|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'phone' => 'nullable|string',
            'birthday' => 'nullable|date',
            'address' => 'nullable|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'role' => 'required|in:admin,staff',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // 🔐 Hash password
        $data['password'] = Hash::make($data['password']);
        $data['status'] = 'active';

        // 📸 Upload profile photo
        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')
                ->store('profile_photos', 'public');
        }

        // 👤 Create user
        $user = User::create($data);

        // 🔥 Send email verification
        event(new Registered($user));

        // 🔄 Reload users list
        $users = User::orderByRaw("CASE WHEN role = 'admin' THEN 1 ELSE 0 END")
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'html' => view('users.table', compact('users'))->render(),
            'message' => 'User created successfully. Verification email sent.',
        ]);
    }

    public function edit(User $user)
    {
        return view('users.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8',
            'first_name' => 'nullable|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'phone' => 'nullable|string',
            'birthday' => 'nullable|date',
            'address' => 'nullable|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'role' => 'required|in:admin,staff',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')
                ->store('profile_photos', 'public');
        }

        $user->update($data);

        $users = User::orderByRaw("CASE WHEN role = 'admin' THEN 1 ELSE 0 END")
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'html' => view('users.table', compact('users'))->render(),
            'message' => 'User updated successfully',
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        $users = User::orderByRaw("CASE WHEN role = 'admin' THEN 1 ELSE 0 END")
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'html' => view('users.table', compact('users'))->render(),
            'message' => 'User deleted successfully',
        ]);
    }
}

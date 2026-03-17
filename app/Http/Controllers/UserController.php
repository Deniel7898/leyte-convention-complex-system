<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        $users_table = view('users.table', compact('users'))->render();
        return view('users.index', compact('users_table'));
    }

    public function table()
    {
        $users = User::all();
        return view('users.table', compact('users'));
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function create()
    {
        return view('users.form');
    }

    public function edit(User $user)
    {
        return view('users.form', compact('user'));
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
            'place' => 'nullable|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'role' => 'required|in:admin,staff',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        $data['password'] = Hash::make($data['password']);

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')
                ->store('profile_photos', 'public');
        }

        User::create($data);

        $users = User::latest()->get();

        return response()->json([
            'html' => view('users.table', compact('users'))->render(),
            'message' => 'User created successfully',
        ]);
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
            'place' => 'nullable|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'role' => 'required|in:admin,staff',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except('password');
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')->store('profile_photos', 'public');
        }

        $user->update($data);

        $users = User::all();

        return response()->json([
            'html' => view('users.table', compact('users'))->render(),
            'message' => 'User updated successfully',
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        $users = User::all();

        return response()->json([
            'html' => view('users.table', compact('users'))->render(),
            'message' => 'User deleted successfully',
        ]);
    }

    public function toggleStatus(User $user)
    {
        $user->status = !$user->status;
        $user->save();

        $users = User::all();

        return response()->json([
            'html' => view('users.table', compact('users'))->render(),
            'message' => 'User status updated successfully',
        ]);
    }
}

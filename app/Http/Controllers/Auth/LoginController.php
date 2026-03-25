<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * The user has been authenticated.
     */
    protected function authenticated(Request $request, $user)
    {
        // Staff not verified → go to verification page
        if ($user->role === 'staff' && !$user->hasVerifiedEmail()) {

            return redirect()->route('verification.notice')
                ->with('warning', 'Please verify your email first.');
        }

        if ($user->role === 'admin') {
            return redirect()->route('verification.notice')
                ->with('warning', 'Please review the verification page.');
        }

        // Normal login
        return redirect()->intended('/home');
    }

    public function logout(Request $request)
    {
        $user = auth()->user();
        if ($user) {
            $user->update([
                'last_seen' => now(),
                'status' => 'inactive',
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

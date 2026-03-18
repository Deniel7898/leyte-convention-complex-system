<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Verified;
use App\Models\User;
use Illuminate\Http\Request;



class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
        $this->middleware('auth')->only('resend');
    }


    /**
     * 🔥 This runs AFTER email is verified
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        // Check signed hash
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            abort(403);
        }

        // Mark email as verified
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        // Log in user if not already
        if (! Auth::check()) {
            Auth::login($user);
        }

        // Redirect to home with success message
        // Inside verify() method
        return redirect()->route('verification.success');
    }

    public function resend(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('home');
        }

        // Send verification email using APP_URL (full URL)
        $user->sendEmailVerificationNotification();

        return redirect()->route('verification.hold')->with('message', 'Verification link sent!');
    }
}

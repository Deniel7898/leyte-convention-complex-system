@extends('layouts.guest')

@section('content')

<style>
    /* Full-page background */
    body,
    html {
        margin: 0;
        padding: 0;
        height: 100%;
        font-family: Arial, sans-serif;
        background: url('{{ asset('images/home_bg/palo_capitol.jpg') }}') center/cover no-repeat;
        background-size: cover;
        position: relative;
    }

    /* Dark overlay */
    body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.25);
        backdrop-filter: blur(5px);
        z-index: 0;
    }

    /* Centered glass card */
    .login-card {
        position: relative;
        width: 550px;
        max-width: 90%;
        padding: 40px 30px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
        text-align: center;
        z-index: 1;
        margin: auto;
    }

    /* Logo */
    .login-card .logo img {
        width: 120px;
        border-radius: 50%;
        margin-bottom: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        background: rgba(255, 255, 255, 0.3);
    }

    /* Title & icon */
    .login-card .title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 12px;
        color: #30ff6b;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        text-shadow: 0 0 6px rgba(48, 255, 107, 0.7);
    }

    .login-card .title svg {
        vertical-align: middle;
    }

    /* Subtitle */
    .login-card .subtitle {
        font-size: 16px;
        margin-bottom: 25px;
        color: rgba(0, 0, 0, 0.8);
        line-height: 1.5;
    }

    /* Dashboard button */
    .dashboard-btn {
        display: inline-block;
        padding: 12px 30px;
        background-color: #4A90E2;
        color: #fff;
        font-weight: bold;
        border-radius: 8px;
        text-decoration: none;
        transition: background 0.3s, transform 0.2s;
    }

    .dashboard-btn:hover {
        background-color: #357ABD;
    }

    /* Footer */
    .login-card footer {
        margin-top: 25px;
        font-size: 12px;
        color: #fff;
    }

    /* Center container */
    .container-center {
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        z-index: 1;
    }
</style>

<div class="container-center">
    <div class="login-card">
        <!-- Logo -->
        <div class="logo">
            <img src="{{ asset('images/logo/leyte_province_logo.jpg') }}" alt="Leyte Logo">
        </div>

        <!-- Title with icon -->
        <h2 class="title">
            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
            </svg>
            Email Verified!
        </h2>

        <!-- Subtitle with security recommendation -->
        <p class="subtitle">
            Thank you for verifying your email.<br>
            Your account is now fully activated and ready to use.<br>
            <strong>For your security, we recommend changing your password immediately.</strong>
        </p>

        <!-- Go to dashboard button -->
        <a href="{{ route('home') }}" class="login-btn dashboard-btn">Go to Dashboard</a>

        <!-- Footer -->
        <footer>
            Maintained by: The Code Crew. {{ now()->year }}
        </footer>
    </div>
</div>

@endsection
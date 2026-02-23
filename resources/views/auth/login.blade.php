@extends('layouts.guest')

@section('content')
<div class="auth-container">
    <div class="auth-card">

        <div class="auth-logo">
        <img src="{{ asset('images/logo/leyte_province_logo.jpg') }}" alt="Leyte Logo">
        </div>

        <!-- Title -->
        <h2 class="auth-title">LCC Inventory Management System</h2>
        <p class="auth-subtitle">
            Leyte Complex Center - Please sign in to continue
        </p>

        <!-- Form -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="form-group">
                <label>Email</label>
                <input type="email" 
                       name="email" 
                       placeholder="Enter email"
                       value="{{ old('email') }}"
                       required>
                @error('email')
                    <small class="error">{{ $message }}</small>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <label>Password</label>
                <input type="password" 
                       name="password" 
                       placeholder="Enter password"
                       required>
                @error('password')
                    <small class="error">{{ $message }}</small>
                @enderror
            </div>

            <!-- Button -->
            <button type="submit" class="login-btn">
                Sign In
            </button>

            <!-- Register -->
            @if (Route::has('register'))
            <p class="register-text">
                New employee?
                <a href="{{ route('register') }}">Create an account</a>
            </p>
            @endif

            <!-- Demo credentials -->
            <div class="demo-box">
                Demo Credentials: <strong>admin / admin</strong>
            </div>
        </form>

    </div>
</div>
@endsection
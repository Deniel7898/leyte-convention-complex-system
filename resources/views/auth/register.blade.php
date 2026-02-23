@extends('layouts.guest')

@section('content')

<div class="auth-container">
    <div class="auth-card">

        <!-- Province Logo -->
        <div class="auth-logo">
            <img src="{{ asset('images/logo/leyte_province_logo.jpg') }}" alt="Leyte Logo">
        </div>

        <!-- Title -->
        <h2 class="auth-title">
            LCC Inventory Management System
        </h2>

        <p class="auth-subtitle">
            Create an account to access the system
        </p>

        <!-- Register Form -->
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div class="form-group">
                <label>Name</label>
                <input type="text"
                       name="name"
                       placeholder="Enter full name"
                       value="{{ old('name') }}"
                       required autofocus>
                @error('name')
                    <small class="error">{{ $message }}</small>
                @enderror
            </div>

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

            <!-- Confirm Password -->
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password"
                       name="password_confirmation"
                       placeholder="Confirm password"
                       required>
            </div>

            <!-- Button -->
            <button type="submit" class="login-btn">
                Register
            </button>

            <!-- Back to Login -->
            <p class="register-text">
                Already have an account?
                <a href="{{ route('login') }}">Sign In</a>
            </p>

        </form>

    </div>
</div>

@endsection
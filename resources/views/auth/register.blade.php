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

        <!-- Registration is disabled; admin users manage accounts -->
        <div class="alert alert-info" role="alert">
            User registration is currently disabled. <br>
            Please contact an administrator if you require an account.
        </div>

    </div>
</div>

@endsection
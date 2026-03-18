@extends('layouts.guest')

@section('content')

<style>
    /* Background layer */
    .bg-layer {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('{{ asset('images/home_bg/palo_capitol.jpg') }}') center/cover no-repeat;
        filter: brightness(0.8) blur(5px);
        z-index: -1;
    }

    .text-center {
        text-align: center;
    }

    .dashboard-btn {
        display: inline-block;
        padding: 10px 25px;
        background-color: #4A90E2;
        /* adjust to your theme */
        color: #fff;
        font-weight: bold;
        border-radius: 5px;
        text-decoration: none;
        transition: background 0.3s;
    }

    .dashboard-btn:hover {
        background-color: #357ABD;
        /* slightly darker on hover */
    }
</style>

<div class="bg-layer"></div>

<div class="container-center">
    <div class="login-card">
        <div class="logo">
            <img src="{{ asset('images/logo/leyte_province_logo.jpg') }}" alt="Leyte Logo">
        </div>

        <!-- Real-time date -->
        <p id="current-date" style="font-size:14px; color: hsl(237, 34%, 26%); margin-bottom:10px;"></p>

        <h2 class="title">LCC Monitoring System</h2>
        <p class="subtitle">
            Leyte Convention Complex -
            @if(auth()->check())
            You are already logged in
            @else
            Please sign in to continue
            @endif
        </p>

        <!-- Check if user is logged in -->
        @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->hasVerifiedEmail()))
        <div class="form-group text-center">
            <a href="{{ route('home') }}" class="login-btn dashboard-btn">Go to Dashboard</a>
        </div>
        @else
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" placeholder="Enter email" value="{{ old('email') }}" required autofocus>
                @error('email') <small class="error">{{ $message }}</small> @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" placeholder="Enter password" required>
                @error('password') <small class="error">{{ $message }}</small> @enderror
            </div>

            <button type="submit" class="login-btn">Sign In</button>
        </form>
        @endauth
    </div>
</div>

@endsection
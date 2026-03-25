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

            @if(auth()->check() && auth()->user()->role === 'staff' && !auth()->user()->hasVerifiedEmail())

                <h2 class="title">Email Verification Required</h2>

                <p class="text-muted" style="font-size:14px; margin-bottom:15px;">
                    Your account is almost ready. Please verify your email to continue.
                </p>

                <p style="font-size:14px; color: hsl(237, 34%, 26%);" class="mb-3">
                    Logged in as:
                    <strong>{{ auth()->user()->email }}</strong>
                </p>

                <!-- Verify Button -->
                <a href="{{ route('verification.hold') }}" class="login-btn dashboard-btn"
                    style="display:block;margin-bottom:10px;">
                    Verify Your Email
                </a>

                <!-- Logout Option -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="background:none;border:none;color:#dc3545;font-size:13px;cursor:pointer;">
                        Logout and use another account
                    </button>
                </form>
            @else
                        <!-- Default title & subtitle for all others -->
                        <h2 class="title">LCC Monitoring System</h2>
                        <p class="subtitle">
                            Leyte Convention Complex -
                            @if(auth()->check())
                                You are already logged in
                            @else
                                Please sign in to continue
                            @endif
                        </p>

                        <!-- Check if user is fully logged in -->
                        @if(auth()->check() && auth()->user()->role === 'admin')
                            <div class="form-group text-center">
                                <a href="{{ route('home') }}" class="login-btn dashboard-btn">Go to Dashboard</a>
                            </div>
                        @elseif(auth()->check() && auth()->user()->role === 'staff' && auth()->user()->hasVerifiedEmail())
                            <div class="form-group text-center">
                                <a href="{{ route('home') }}" class="login-btn dashboard-btn">Go to Dashboard</a>
                            </div>
                        @else
                            <!-- Normal login form goes here -->
                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input id="email" type="email" name="email" placeholder="Enter email" value="{{ old('email') }}"
                                        required autofocus>
                                    @error('email') <small class="error">{{ $message }}</small> @enderror
                                </div>

                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input id="password" type="password" name="password" placeholder="Enter password" required>
                                    @error('password') <small class="error">{{ $message }}</small> @enderror
                                </div>

                                <button type="submit" class="login-btn">Sign In</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif
@endsection
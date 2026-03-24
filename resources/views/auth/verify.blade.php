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
        background: url('{{ asset(' images/home_bg/palo_capitol.jpg') }}') center/cover no-repeat;
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
        background: rgba(0, 0, 0, 0.2);
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
        width: 100px;
        border-radius: 50%;
        margin-bottom: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        background: rgba(255, 255, 255, 0.3);
    }

    /* Title & subtitle */
    .login-card .title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 8px;
        text-shadow: 1px 1px 4px rgba(255, 255, 255, 1);
    }

    .login-card .subtitle {
        font-size: 14px;
        margin-bottom: 20px;
        color: rgba(0, 0, 0, 0.7);
    }

    /* Resend button */
    .login-card .resend-btn {
        background: rgba(255, 255, 255, 0.55);
        border: none;
        padding: 10px 0;
        width: 100%;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
        margin-top: 15px;
        color: #555;
    }

    .login-card .resend-btn:hover {
        background: hsl(237, 34%, 26%);
        color: #fff;
    }

    /* Messages */
    .login-card .alert {
        margin-top: 15px;
        padding: 10px;
        border-radius: 5px;
        font-size: 14px;
    }

    .alert-success {
        background-color: #d1e7dd;
        color: #0f5132;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #842029;
    }

    /* Footer */
    .login-card footer {
        margin-top: 20px;
        font-size: 12px;
        color: #fff;
    }

    .container-center {
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        z-index: 1;
    }

    .skip-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        color: rgba(255, 255, 255, 0.55);
        /* grey text */
        text-decoration: none;
        /* no underline */
        font-weight: 600;
        transition: 0.3s;
        font-size: 14px;
    }

    .skip-btn:hover {
        color: white;
        /* slightly darker grey on hover */
    }
</style>

<div class="container-center">
    <div class="login-card">
        <!-- Skip button -->
        @if(auth()->user()->role === 'admin')
        <a href="{{ route('home') }}" class="skip-btn">Skip</a>
        @endif

        <!-- Logo -->
        <div class="logo">
            <img src="{{ asset('images/logo/leyte_province_logo.jpg') }}" alt="Leyte Logo">
        </div>

        <h2 class="title">Verify Your Email</h2>
        <p class="subtitle">
            Please check your email for a verification link.<br>
            Once verified, this page will redirect you automatically.<br>
        </p>

        <!-- Resend form -->
        <form method="POST" action="{{ route('verification.resend') }}" id="resendForm">
            @csrf
            <button type="submit" class="resend-btn" id="resendBtn">Click here to resend</button>
        </form>

        <p id="verification-status" class="mt-4 fw-600" style="color:hsl(237, 34%, 26%);">Waiting for verification...</p>

        <!-- Messages -->
        @if(session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Footer -->
        <footer>
            Maintained by: The Code Crew. {{ now()->year }}
        </footer>
    </div>
</div>
<script>
    async function checkEmailVerification() {
        try {
            // Fetch verification status from your dedicated route
            const res = await fetch(`/check-verification-status?email={{ auth()->user()->email }}`);
            const data = await res.json();

            if (data.verified) {
                // Redirect to dashboard immediately
                window.location.href = '/home';
            } else {
                // Keep checking every 3 seconds until verified
                setTimeout(checkEmailVerification, 3000);
            }
        } catch (err) {
            console.error('Verification check failed', err);
            setTimeout(checkEmailVerification, 5000); // retry in case of error
        }
    }

    // Run automatically
    checkEmailVerification();
</script>

<script>
    const resendForm = document.getElementById('resendForm');
    const resendBtn = document.getElementById('resendBtn');
    const resendInfo = document.getElementById('resendInfo');

    const cooldown = 5 * 60; // 5 minutes in seconds
    let cooldownTimer;

    // Check if there's a stored cooldown in sessionStorage (persists across refresh)
    let lastResend = localStorage.getItem('lastResendTime');
    if (lastResend) {
        const elapsed = Math.floor(Date.now() / 1000) - lastResend;
        if (elapsed < cooldown) startCooldown(cooldown - elapsed);
    }

    resendForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Disable immediately
        resendBtn.disabled = true;

        try {
            const formData = new FormData(resendForm);
            const res = await fetch(resendForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (res.ok) {
                // Store resend time
                localStorage.setItem('lastResendTime', Math.floor(Date.now() / 1000));
                startCooldown(cooldown);
                alert('Verification email sent!');
            } else {
                const data = await res.json();
                alert(data.message || 'Error sending email');
                resendBtn.disabled = false;
            }

        } catch (err) {
            console.error(err);
            alert('Error sending email');
            resendBtn.disabled = false;
        }
    });

    function startCooldown(seconds) {
        let timeLeft = seconds;
        resendBtn.disabled = true;

        cooldownTimer = setInterval(() => {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            resendBtn.innerHTML = `<span style="font-size:0.85em;">You can resend in ${minutes}:${seconds < 10 ? '0' : ''}${seconds}</span>`;
            timeLeft--;

            if (timeLeft < 0) {
                clearInterval(cooldownTimer);
                resendBtn.disabled = false;
                resendBtn.innerText = 'Click here to resend';
            }
        }, 1000);
    }
</script>

@endsection
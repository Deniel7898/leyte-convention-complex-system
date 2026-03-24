@extends('layouts.guest')

@section('content')

<style>
/* Full-page background */
body, html {
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
    top: 0; left: 0;
    width: 100%; height: 100%;
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
    box-shadow: 0 8px 32px rgba(0,0,0,0.25);
    text-align: center;
    z-index: 1;
    margin: auto;
}

/* Logo */
.login-card .logo img {
    width: 100px;
    border-radius: 50%;
    margin-bottom: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    background: rgba(255,255,255,0.3);
}

/* Titles */
.login-card .title {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 8px;
    text-shadow: 1px 1px 4px rgba(255,255,255,1);
}

.login-card .subtitle {
    font-size: 14px;
    margin-bottom: 20px;
    color: rgba(0,0,0,0.7);
}

/* Resend button */
.login-card .resend-btn {
    background: rgba(255,255,255,0.55);
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
    background: hsl(237,34%,26%);
    color: #fff;
}

/* Messages */
.login-card .alert {
    margin-top: 15px;
    padding: 10px;
    border-radius: 5px;
    font-size: 14px;
}

.alert-success { background-color: #d1e7dd; color: #0f5132; }
.alert-danger { background-color: #f8d7da; color: #842029; }
.alert-info { background-color: #cff4fc; color: #055160; }

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
    color: rgba(255,255,255,0.55);
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: 0.3s;
}

.skip-btn:hover { color: white; }

/* Spinner */
#loading-spinner {
    position: fixed;
    top:0; left:0;
    width:100vw; height:100vh;
    background: rgba(255,255,255,0.7);
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
}

#loading-spinner.active { display: flex; }

.spinner {
    border: 6px solid #f3f3f3;
    border-top: 6px solid hsl(237,34%,25%);
    border-radius: 50%;
    width: 60px;
    height: 60px;
    animation: spin 1s linear infinite;
}

@keyframes spin { 0% {transform:rotate(0deg);} 100%{transform:rotate(360deg);} }
</style>

<div class="container-center">
    <div class="login-card">

        <!-- Skip -->
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
            Once verified, this page will redirect you automatically.
        </p>

        <!-- Resend form -->
        <form method="POST" action="{{ route('verification.resend') }}" id="resendForm">
            @csrf
            <button type="submit" class="resend-btn" id="resendBtn">Click here to resend</button>
        </form>

        <!-- Message placeholder -->
        <div id="jsMessage"></div>

        <p id="verification-status" class="mt-4 fw-600" style="color:hsl(237, 34%, 26%);">Waiting for verification...</p>

        <!-- Footer -->
        <footer>
            Maintained by: The Code Crew. {{ now()->year }}
        </footer>
    </div>
</div>

<!-- Loading Spinner -->
<div id="loading-spinner">
    <div class="spinner"></div>
</div>

<script>
// Check email verification status
async function checkEmailVerification() {
    try {
        const res = await fetch(`/check-verification-status?email={{ auth()->user()->email }}`);
        const data = await res.json();
        if(data.verified) {
            window.location.href = '/home';
        } else {
            setTimeout(checkEmailVerification, 3000);
        }
    } catch(err) {
        console.error(err);
        setTimeout(checkEmailVerification, 5000);
    }
}
checkEmailVerification();

// Resend button cooldown
const resendForm = document.getElementById('resendForm');
const resendBtn = document.getElementById('resendBtn');
const jsMessage = document.getElementById('jsMessage');
const loadingSpinner = document.getElementById('loading-spinner');

const cooldown = 5 * 60; // 5 minutes
let cooldownTimer;

// Check if cooldown exists in localStorage
let lastResend = localStorage.getItem('lastResendTime');
if(lastResend) {
    const elapsed = Math.floor(Date.now()/1000) - lastResend;
    if(elapsed < cooldown) startCooldown(cooldown - elapsed);
}

// Handle resend
resendForm.addEventListener('submit', async function(e){
    e.preventDefault();

    resendBtn.disabled = true;
    loadingSpinner.classList.add('active');
    jsMessage.innerHTML = `<div class="alert alert-info">Sending verification email...</div>`;

    try {
        const formData = new FormData(resendForm);
        const res = await fetch(resendForm.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        if(res.ok) {
            localStorage.setItem('lastResendTime', Math.floor(Date.now()/1000));
            jsMessage.innerHTML = `<div class="alert alert-success">Verification email sent!</div>`;
            startCooldown(cooldown);
        } else {
            const data = await res.json();
            jsMessage.innerHTML = `<div class="alert alert-danger">${data.message || 'Error sending email'}</div>`;
            resendBtn.disabled = false;
        }
    } catch(err) {
        console.error(err);
        jsMessage.innerHTML = `<div class="alert alert-danger">Error sending email</div>`;
        resendBtn.disabled = false;
    } finally {
        loadingSpinner.classList.remove('active');
    }
});

// Cooldown countdown
function startCooldown(seconds) {
    let timeLeft = seconds;
    resendBtn.disabled = true;

    cooldownTimer = setInterval(()=>{
        const minutes = Math.floor(timeLeft/60);
        const secs = timeLeft % 60;
        resendBtn.innerHTML = `<span style="font-size:0.85em;">You can resend in ${minutes}:${secs<10?'0':''}${secs}</span>`;
        timeLeft--;

        if(timeLeft < 0){
            clearInterval(cooldownTimer);
            resendBtn.disabled = false;
            resendBtn.innerText = 'Click here to resend';
        }
    }, 1000);
}
</script>

@endsection
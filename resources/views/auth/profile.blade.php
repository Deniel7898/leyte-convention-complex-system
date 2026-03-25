@extends('layouts.app')

@section('page_title', 'My Profile')

@section('content')

    <style>
        /* ===== CUSTOM MINIMAL ===== */
        .modern-input {
            border: 1px solid #e0e0e0;
            font-size: 16px;
        }

        .modern-input:focus {
            border-color: hsl(238, 35%, 25%);
            box-shadow: 0 0 0 0.15rem rgba(63, 81, 181, 0.15);
        }

        /* PROFILE IMAGE */
        .profile-wrapper {
            width: 120px;
            margin: auto;
            position: relative;
        }

        .profile-wrapper img,
        .profile-placeholder {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
        }

        .profile-placeholder {
            background: #6c757d;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        /* EDIT BUTTON */
        .edit-icon {
            position: absolute;
            bottom: 5px;
            right: 5px;
            width: 34px;
            height: 40px;
            background: hsl(238, 35%, 25%);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        /* FLOAT ALERT */
        .floating-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        }

        /* ANIMATION */
        .alert-modern {
            opacity: 0;
            transform: translateX(50px);
            animation: slideIn 0.4s ease forwards;
        }

        @keyframes slideIn {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .alert-modern.hide {
            animation: fadeOut 0.5s forwards;
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                transform: translateX(50px);
            }
        }
    </style>

    <div class="container mt-4">

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4">

                <h4 class="fw-semibold mb-1">My Profile</h4>
                <p class="text-muted mb-4">Manage your personal information</p>

                {{-- ALERTS --}}
                @if ($message = Session::get('success'))
                    <div
                        class="alert alert-success alert-dismissible d-flex align-items-center gap-2 shadow floating-alert alert-modern rounded-3">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                class="bi bi-check-circle" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                <path
                                    d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05" />
                            </svg>
                        </span>
                        <div class="flex-grow-1">
                            <strong>Success!</strong> {{ $message }}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($message = Session::get('error'))
                    <div
                        class="alert alert-danger alert-dismissible d-flex align-items-center gap-2 shadow floating-alert alert-modern rounded-3">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                class="bi bi-x-circle" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                <path
                                    d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                            </svg>
                        </span>
                        <div class="flex-grow-1">
                            <strong>Error!</strong> {{ $message }}
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    {{-- PROFILE IMAGE --}}
                    <div class="profile-wrapper mb-4 text-center">
                        <label for="profile_photo">
                            @if(auth()->user()->profile_photo)
                                <img id="previewImage" src="{{ asset('storage/' . auth()->user()->profile_photo) }}">
                            @else
                                <div id="previewImage" class="profile-placeholder">
                                    N/A
                                </div>
                            @endif

                            <div class="edit-icon shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-pencil-square" viewBox="0 0 16 16">
                                    <path
                                        d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                    <path fill-rule="evenodd"
                                        d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                </svg>
                            </div>
                        </label>

                        <input type="file" name="profile_photo" id="profile_photo" hidden>
                    </div>

                    {{-- NAME --}}
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="first_name"
                                class="form-control modern-input rounded-3 @error('first_name') is-invalid @enderror"
                                placeholder="First Name" value="{{ old('first_name', auth()->user()->first_name) }}">
                            @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <input type="text" name="middle_name"
                                class="form-control modern-input rounded-3 @error('middle_name') is-invalid @enderror"
                                placeholder="Middle Name" value="{{ old('middle_name', auth()->user()->middle_name) }}">
                            @error('middle_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <input type="text" name="last_name"
                                class="form-control modern-input rounded-3 @error('last_name') is-invalid @enderror"
                                placeholder="Last Name" value="{{ old('last_name', auth()->user()->last_name) }}">
                            @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- CONTACT --}}
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <input type="email" name="email"
                                class="form-control modern-input rounded-3 @error('email') is-invalid @enderror"
                                placeholder="Email" value="{{ old('email', auth()->user()->email) }}">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <input type="text" name="phone"
                                class="form-control modern-input rounded-3 @error('phone') is-invalid @enderror"
                                placeholder="Phone" value="{{ old('phone', auth()->user()->phone) }}">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- OTHER --}}
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <input type="date" name="birthday"
                                class="form-control modern-input rounded-3 @error('birthday') is-invalid @enderror"
                                value="{{ old('birthday', auth()->user()->birthday ? auth()->user()->birthday->format('Y-m-d') : '') }}">
                            @error('birthday') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <input type="text" name="address"
                                class="form-control modern-input rounded-3 @error('address') is-invalid @enderror"
                                placeholder="Address" value="{{ old('address', auth()->user()->address) }}">
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- PASSWORD --}}
                    <div class="row g-3 mt-1">
                        <div class="col-md-6">
                            <input type="password" name="password"
                                class="form-control modern-input rounded-3 @error('password') is-invalid @enderror"
                                placeholder="New Password">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <input type="password" name="password_confirmation" class="form-control modern-input rounded-3"
                                placeholder="Confirm Password">
                        </div>
                    </div>

                    {{-- BUTTON --}}
                    <div class="mt-4">
                        <button type="submit" class="btn w-100 text-white rounded-3 shadow-sm fs-5 fw-bold"
                            style="background: hsl(238, 35%, 25%);">
                            Update Profile
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>

    {{-- IMAGE PREVIEW --}}
    <script>
        document.getElementById('profile_photo').addEventListener('change', function (e) {
            const [file] = e.target.files;
            if (file) {
                document.getElementById('previewImage').src = URL.createObjectURL(file);
            }
        });
    </script>

    {{-- AUTO HIDE ALERT --}}
    <script>
        setTimeout(() => {
            document.querySelectorAll('.floating-alert').forEach(alert => {
                alert.classList.add('hide');
                setTimeout(() => alert.remove(), 500);
            });
        }, 10000);
    </script>

@endsection
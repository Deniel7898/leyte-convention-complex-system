@extends('layouts.app')

@section('page_title', 'My Profile')

@section('content')

<div class="card-styles pt-30">
    <div class="card-style-3 mb-30">
        <div class="card-content">

            @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> {{ $message }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="row">
                    <!-- end col -->
                    <div class="col-12 col-md-4">
                        <div class="input-style-1">
                            <label for="first_name">{{ __('First Name') }}</label>
                            <input type="text" @error('first_name') class="form-control is-invalid" @enderror name="first_name"
                                id="first_name" placeholder="{{ __('First Name') }}"
                                value="{{ old('first_name', auth()->user()->first_name) }}">
                            @error('first_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-12 col-md-4">
                        <div class="input-style-1">
                            <label for="middle_name">{{ __('Middle Name') }}</label>
                            <input type="text" @error('middle_name') class="form-control is-invalid" @enderror name="middle_name"
                                id="middle_name" placeholder="{{ __('Middle Name') }}"
                                value="{{ old('middle_name', auth()->user()->middle_name) }}">
                            @error('middle_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-12 col-md-4">
                        <div class="input-style-1">
                            <label for="last_name">{{ __('Last Name') }}</label>
                            <input type="text" @error('last_name') class="form-control is-invalid" @enderror name="last_name"
                                id="last_name" placeholder="{{ __('Last Name') }}"
                                value="{{ old('last_name', auth()->user()->last_name) }}">
                            @error('last_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-12">
                        <div class="input-style-1">
                            <label for="email">{{ __('Email') }}</label>
                            <input @error('email') class="form-control is-invalid" @enderror type="email"
                                name="email" id="email" placeholder="{{ __('Email') }}"
                                value="{{ old('email', auth()->user()->email) }}" required>
                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-12 col-md-6">
                        <div class="input-style-1">
                            <label for="phone">{{ __('Phone') }}</label>
                            <input type="text" @error('phone') class="form-control is-invalid" @enderror name="phone"
                                id="phone" placeholder="{{ __('Phone') }}"
                                value="{{ old('phone', auth()->user()->phone) }}">
                            @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-12 col-md-6">
                        <div class="input-style-1">
                            <label for="birthday">{{ __('Birthday') }}</label>
                            <input type="date" @error('birthday') class="form-control is-invalid" @enderror name="birthday"
                                id="birthday"
                                value="{{ old('birthday', auth()->user()->birthday ? auth()->user()->birthday->format('Y-m-d') : '') }}">
                            @error('birthday')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-12">
                        <div class="input-style-1">
                            <label for="place">{{ __('Place') }}</label>
                            <input type="text" @error('place') class="form-control is-invalid" @enderror name="place"
                                id="place" placeholder="{{ __('Place') }}"
                                value="{{ old('place', auth()->user()->place) }}">
                            @error('place')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-12">
                        <div class="input-style-1">
                            <label for="profile_photo">{{ __('Profile Photo') }}</label>
                            <input type="file" @error('profile_photo') class="form-control is-invalid" @enderror name="profile_photo"
                                id="profile_photo" accept="image/*">
                            @if(auth()->user()->profile_photo)
                            <small class="text-muted">Current: <a href="{{ asset('storage/' . auth()->user()->profile_photo) }}" target="_blank">View Image</a></small>
                            @endif
                            @error('profile_photo')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-12">
                        <div class="input-style-1">
                            <label for="password">{{ __('New password') }}</label>
                            <input type="password" @error('password') class="form-control is-invalid"
                                @enderror name="password" id="password" placeholder="{{ __('New password') }}">
                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-12">
                        <div class="input-style-1">
                            <label for="password_confirmation">{{ __('New password confirmation') }}</label>
                            <input type="password" @error('password') class="form-control is-invalid"
                                @enderror name="password_confirmation" id="password_confirmation"
                                placeholder="{{ __('New password confirmation') }}">
                        </div>
                    </div>
                    <!-- end col -->
                    <div class="col-12">
                        <div class="button-group d-flex justify-content-center flex-wrap">
                            <button type="submit" class="text-white main-btn btn-hover w-100 text-center" style="background: hsl(238, 35%, 25%);">
                                {{ __('Submit') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
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

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="row">
                    <div class="col-12">
                        <div class="input-style-1">
                            <label for="name">{{ __('Name') }}</label>
                            <input type="text" @error('name') class="form-control is-invalid" @enderror name="name"
                                id="name" placeholder="{{ __('Name') }}"
                                value="{{ old('name', auth()->user()->name) }}" required>
                            @error('name')
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
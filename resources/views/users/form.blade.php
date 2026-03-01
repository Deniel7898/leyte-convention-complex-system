@php
    // $user may or may not be defined; use old() fallback
@endphp

<div class="row">
    <div class="col-md-4 col-12">
        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first_name" class="modern-input" required
                   value="{{ old('first_name', $user->first_name ?? '') }}">
            @error('first_name')<small class="error">{{ $message }}</small>@enderror
        </div>
    </div>

    <div class="col-md-4 col-12">
        <div class="form-group">
            <label>Middle Name</label>
            <input type="text" name="middle_name" class="modern-input"
                   value="{{ old('middle_name', $user->middle_name ?? '') }}">
            @error('middle_name')<small class="error">{{ $message }}</small>@enderror
        </div>
    </div>

    <div class="col-md-4 col-12">
        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name" class="modern-input" required
                   value="{{ old('last_name', $user->last_name ?? '') }}">
            @error('last_name')<small class="error">{{ $message }}</small>@enderror
        </div>
    </div>

    <div class="col-md-6 col-12">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="modern-input" required
                   value="{{ old('email', $user->email ?? '') }}">
            @error('email')<small class="error">{{ $message }}</small>@enderror
        </div>
    </div>

    <div class="col-md-6 col-12">
        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" class="modern-input"
                   value="{{ old('phone', $user->phone ?? '') }}">
            @error('phone')<small class="error">{{ $message }}</small>@enderror
        </div>
    </div>

    <div class="col-md-6 col-12">
        <div class="form-group">
            <label>{{ isset($user) ? 'New Password' : 'Password' }}</label>
            <input type="password" name="password" class="modern-input" {{ isset($user) ? '' : 'required' }}>
            @error('password')<small class="error">{{ $message }}</small>@enderror
        </div>
    </div>

    <div class="col-12">
        <div class="form-group">
            <label>{{ isset($user) ? 'Confirm New Password' : 'Confirm Password' }}</label>
            <input type="password" name="password_confirmation" class="modern-input" {{ isset($user) ? '' : 'required' }}>
        </div>
    </div>

    <div class="col-12">
        <div class="form-group">
            <label class="d-block">
                <input type="checkbox" name="is_admin" value="1" {{ old('is_admin', $user->is_admin ?? false) ? 'checked' : '' }}>
                Administrator
            </label>
        </div>
    </div>
</div>

<form action="{{ isset($user) ? route('users.update', $user->id) : route('users.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($user))
    @method('PUT')
    @endif

    <div class="modal-header" style="background-color: rgb(43, 45, 87);">
        <h5 class="modal-title text-white">{{ isset($user) ? 'Edit' : 'New' }} User</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="first_name" class="form-label required">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $user->first_name ?? '' }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="middle_name" class="form-label bold-label">Middle Name</label>
                    <input type="text" class="form-control" id="middle_name" name="middle_name" value="{{ $user->middle_name ?? '' }}">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="last_name" class="form-label required">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $user->last_name ?? '' }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="email" class="form-label required">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ $user->email ?? '' }}" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label bold-label">Password</label>
                    <div class="form-control bg-light text-muted" style="font-size: 12px;">
                        Password will be automatically generated and sent to the user's email.
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="phone" class="form-label ">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="{{ $user->phone ?? '' }}">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="birthday" class="form-label">Birthday</label>
                    <input type="date" class="form-control" id="birthday" name="birthday"
                        value="{{ isset($user) && $user->birthday ? $user->birthday->format('Y-m-d') : '' }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address" name="address" value="{{ $user->address ?? '' }}">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="staff" {{ ($user->role ?? '') == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="admin" {{ ($user->role ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="profile_photo" class="form-label">Profile Photo</label>
                    <input type="file" class="form-control" id="profile_photo" name="profile_photo" accept="image/*">
                    @if(isset($user) && $user->profile_photo)
                    <small class="text-muted">Current: <a href="{{ asset('storage/' . $user->profile_photo) }}" target="_blank">View Image</a></small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn text-white" style="background-color: rgb(43, 45, 87);">Create User</button>
    </div>
</form>
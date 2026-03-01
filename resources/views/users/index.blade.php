@extends('layouts.app')

@section('page_title', 'Users')

@section('content')

<!-- ========== title-wrapper start ========== -->
<div class="title-wrapper pt-30">
    <div class="row align-items-center">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <!-- Left: Page Title -->
                <div class="title">
                    <div>
                        <h2>Users List</h2>
                        <p class="text-muted mb-0 text-sm">Manage all system users.</p>
                    </div>
                </div>

                <!-- Right: Add Button -->
                <a href="{{ route('users.create') }}" class="btn text-white"
                    style="background-color: hsl(237, 34%, 30%); padding:8px 16px; display:inline-block;" 
                    onmouseover="this.style.backgroundColor='hsl(237, 34%, 40%)'" onmouseout="this.style.backgroundColor='hsl(237, 34%, 30%)'">
                    + Add New User
                </a>
            </div>
        </div>
    </div>
</div>
<!-- ========== title-wrapper end ========== -->

<div class="card shadow-sm border-0 rounded-4 card-styles mt-2">
    <div class="card-body p-0">
        <div class="table-responsive rounded-4">
            <table class="table align-middle table-hover" id="users_table">
                <thead class="bg-light">
                    <tr class="text-uppercase text-muted small">
                        <th>{{ __('#') }}</th>
                        <th>{{ __('Full Name') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Phone') }}</th>
                        <th class="text-center">{{ __('Admin') }}</th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="users-table-body" class="text-muted small">
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <h6 class="text-sm">#{{ $user->id }}</h6>
                        </td>
                        <td>
                            <p>{{ $user->full_name }}</p>
                        </td>
                        <td>
                            <p>{{ $user->email }}</p>
                        </td>
                        <td>
                            <p>{{ $user->phone ?? '-' }}</p>
                        </td>
                        <td class="text-center">
                            @if($user->is_admin)
                                <span class="badge-status status-approved">Yes</span>
                            @else
                                &mdash;
                            @endif
                        </td>
                        <td class="text-center">
                            {{-- view disabled for now --}}
                            <a href="{{ route('users.edit', $user) }}" title="Edit User" class="btn p-0 border-0 bg-transparent text-gray me-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen w-4 h-4">
                                    <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"></path>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('users.destroy', $user) }}" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Delete User" class="btn p-0 border-0 bg-transparent text-danger" onclick="return confirm('Are you sure?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2 lucide-trash-2 w-4 h-4">
                                        <path d="M3 6h18"></path>
                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                        <line x1="10" x2="10" y1="11" y2="17"></line>
                                        <line x1="14" x2="14" y1="11" y2="17"></line>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
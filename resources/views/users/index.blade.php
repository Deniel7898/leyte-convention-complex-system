@extends('layouts.app')

@section('page_title', 'Users')

@section('content')

<div class="p-3 bg-white border rounded-3 mt-30">
    <div class="row align-items-center gx-3">
        <div class="col">
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                    </svg>
                </span>
                <input type="search" id="user-search" class="form-control" placeholder="Search by name or email...">
            </div>
        </div>

        <div class="col-auto" style="min-width: 140px;">
            <select id="role-filter" class="form-select">
                <option>All Roles</option>
                <option>admin</option>
                <option>staff</option>
            </select>
        </div>

        <div class="col-auto">
            <div class="col-auto add-user" data-url="{{ route('users.create') }}">
                <button class="btn px-4 text-white" style="background-color: hsl(237, 34%, 30%);" onmouseover="this.style.backgroundColor='hsl(237, 34%, 40%)'" onmouseout="this.style.backgroundColor='hsl(237, 34%, 30%)'">
                    + Add New User
                </button>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 rounded-4 card-styles mt-3">
    <div class="card-body p-0">
        <div class="table-responsive rounded-4">
            <table class="table align-middle table-hover" id="users_table">
                <thead class="bg-light">
                    <tr class="text-uppercase text-muted small">
                        <th>{{ __('#') }}</th>
                        <th>{{ __('Role') }}</th>
                        <th>{{ __('Last Name') }}</th>
                        <th>{{ __('First Name') }}</th>
                        <th>{{ __('Middle Initial') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Phone') }}</th>
                        <th>{{ __('Birthday') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody id="users-table-body" class="text-muted small">
                    {!!$users_table!!}
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="users_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>

<!-- Loading Spinner -->
<div id="loading-spinner">
    <div class="spinner"></div>
</div>

@endsection
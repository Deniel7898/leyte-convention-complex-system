@extends('layouts.app')

@section('page_title', 'Create User')

@section('content')
<div class="card-styles pt-30">
    <div class="card-style-3 mb-30">
        <div class="card-content">
            <h3 class="page-title mb-20">New User</h3>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                @include('users.form')
                <div class="button-group d-flex justify-content-center flex-wrap mt-20">
                    <button type="submit" class="text-white main-btn btn-hover w-100 text-center" style="background: #2563eb;">
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

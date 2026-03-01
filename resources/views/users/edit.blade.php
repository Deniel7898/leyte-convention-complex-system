@extends('layouts.app')

@section('page_title', 'Edit User')

@section('content')
<div class="card-styles pt-30">
    <div class="card-style-3 mb-30">
        <div class="card-content">
            <h3 class="page-title mb-20">Edit User</h3>
            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf
                @method('PATCH')
                @include('users.form')
                <div class="button-group d-flex justify-content-center flex-wrap mt-20">
                    <button type="submit" class="text-white main-btn btn-hover w-100 text-center" style="background: #2563eb;">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

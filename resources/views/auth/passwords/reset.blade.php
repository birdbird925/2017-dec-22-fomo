@extends('layouts.app')

@section('logo-class')
    fixed
@endsection

@section('content')
    <div class="page-title title">
        Reset Password
    </div>
    <div class="page-content" id="forget-password-wrapper">
        <form action="/password/reset" method="POST">
            {{ csrf_field() }}
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <label for="email">Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="email">Confirm Password</label>
                <input type="password" name="password_confirmation" required>
            </div>
            <input type="submit" value="Reset Password">
        </form>
    </div>
@endsection

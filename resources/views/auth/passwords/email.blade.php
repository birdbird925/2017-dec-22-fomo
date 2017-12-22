@extends('layouts.app')

@section('logo-class')
    fixed
@endsection

@section('content')
    <div class="page-title title">
        Forget Password
    </div>
    <div class="page-content" id="forget-password-wrapper">
        <form action="/password/email" method="POST">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required>
            </div>
            <input type="submit" value="Send Password Reset Link">
        </form>
    </div>
@endsection

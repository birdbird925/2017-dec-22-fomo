@extends('layouts.admin')

@section('page-direction')
    Account
@endsection

@section('content')
    <div class="col-xs-12">
        <div class="card">
            <div class="header">
                <h4 class="title">My Account</h4>
            </div>
            <div class="content">
                <div class="row">
                    <div class="col-sm-5">
                        <form action="/account/email" method="post">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>Old email</label>
                                <input type="email" class="form-control" value="{{Auth::user()->email}}" disabled>
                            </div>
                            <div class="form-group">
                                <label>New email</label>
                                <input type="email" name="email" class="form-control">
                            </div>
                            @if($errors->has('email'))
                                @include('layouts.partials.alert')
                            @endif
                            <input type="submit" class="btn btn-primary" value="save">
                        </form>
                    </div>
                    <div class="col-sm-5 col-sm-offset-1">
                        <form action="/account/password" method="post">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>Old password</label>
                                <input type="password" name="Old_Password" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>New password</label>
                                <input type="password" name="New_Password" class="form-control">
                            </div>
                            @if($errors->has('New_Password') || $errors->has('Old_Password'))
                                @include('layouts.partials.alert')
                            @endif
                            <input type="submit" class="btn btn-primary" value="save">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="/js/lightslider.min.js"></script>
@endpush

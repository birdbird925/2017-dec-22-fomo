@extends('layouts.app')

@section('logo-class')
    fixed
@endsection

@section('facebook.pixel.event')
    fbq('track', 'Purchase');
@endsection

@section('content')
    <div class="page-title title">
        Thanks You
    </div>
    <div class="page-content small" style="text-align: center">
        Your order is successfully placed.
        <br>
        @if(Auth::check())
            <a href="/account" style="margin-right: 25px;">View order</a>
        @endif
        <a href="/customize">Continue to shop</a>
    </div>
@endsection

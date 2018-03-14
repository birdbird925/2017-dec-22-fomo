@extends('layouts.app')

@section('logo-class')
    fixed
@endsection

@section('facebook.pixel.event')
    fbq('track', 'Purchase');
@endsection

@section('content')
    <div class="page-title title">
        Thanks you!!
    </div>
    <div class="page-content small">
        Your order had been confirmed!!
    </div>
@endsection

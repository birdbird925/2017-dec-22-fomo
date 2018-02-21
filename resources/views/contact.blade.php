@extends('layouts.app')

@section('logo-class')
    fixed
@endsection

@section('footer-class')
    mobile-hide
@endsection

@section('content')
    <div class="contact-wrapper">
        <div class="page-title title">
            Contact
        </div>
        <div class="contact-form">
            <form action="/contact" method="post">
                {{ csrf_field() }}
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email">
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" cols="30" rows="5"></textarea>
                </div>
                <input type="submit" value="send">
            </form>
        </div>
        <div class="sepecial-regarding">
            <label>Or send us email regarding to</label>
            <br>
            <a href="mailto:hello@fomo.watch">PARTNERSHIP, RETAIL, PRESS</a>
        </div>
    </div>
@endsection


@push('head-scripts')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
@endpush

@extends('layouts.app')

@section('logo-class')
    fixed
@endsection

@section('content')
    <div class="page-title title">
        {{$page->page_title}}
    </div>
    <div class="page-content {{$page->page_content_size}}">
        {!!$page->page_content!!}
    </div>
@endsection

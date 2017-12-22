@extends('layouts.admin')

@section('page-direction')
    <a href="/admin/customize/type">Type</a> / {{$type->name}}
@endsection

@section('customize-sidebar')
    active
@endsection

@section('customize-dropdown')
    in
@endsection

@section('type-sidebar')
    active
@endsection

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="header">
                <h4 class="title">Edit Customize Type</h4>
            </div>
            <div class="content">
                <div class="row">
                    <div class="col-sm-6">
                        <form action="/admin/customize/type/{{$type->id}}" method="post">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" value="{{old('name') ? old('name') :$type->name}}">
                            </div>
                            <div class="form-group">
                                <label>Price</label>
                                <input type="number" name="price" class="form-control" value="{{old('price') ? old('price') : $type->price}}">
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control ckeditor" id="ckeditro">{!!old('description') ? old('description') : $type->description!!}</textarea>
                            </div>
                            @include('layouts.partials.alert')
                            <input type="submit" class="btn btn-primary" value="Update">
                            <a href="/admin/customize/type" class="btn btn-default">Cancel</a>
                        </form>
                    </div>
                    <div class="col-sm-5 col-sm-offset-1">
                        <div class="note-wrapper">
                            <div class="note-title">
                                Steps list
                            </div>
                            <ol>
                                @foreach($steps as $step)
                                    @if($step->type_id == '' || ($step->type_id == $type->id))
                                        <li>
                                            <a href="/admin/customize/step/{{$step->id}}">
                                                {{$step->title}}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="/js/admin/ckeditor/ckeditor.js"></script>
@endpush

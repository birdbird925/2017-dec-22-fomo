@extends('layouts.admin')

@section('page-direction')
    <a href="/admin/cms">CMS</a> / Slider
@endsection

@section('cms-sidebar')
    active
@endsection

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="header">
                <h4 class="title">{{isset($content) ? 'Edit' : 'Create'}} Menu</h4>
            </div>
            <div class="content">
                <div class="row">
                    <form class="col-md-6" action="/admin/cms/menu{{isset($content) ? '/'.$content[0]->id : ''}}" method="post">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label>Type</label>
                            <select name="type" class="form-control">selected
                                <option value="nav" {{ isset($content) ? ($content[0]->type == 'nav' ? 'selected' : '') : (app('request')->input('type') == 'nav' ? 'selected' : '') }}>Main menu</option>
                                <option value="footer" {{ isset($content) ? ($content[0]->type == 'footer' ? 'selected' : '') : (app('request')->input('type') == 'footer' ? 'selected' : '') }}>Footer menu</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="text" class="form-control" value="{{old('title') ? old('title') : (isset($content) ? $content[0]->text : '')}}">
                        </div>

                        <div class="form-group">
                            <label>Link</label>
                            <input type="text" name="link" class="form-control" value="{{old('link') ? old('link') : (isset($content) ? ltrim($content[0]->link, '/') : '')}}">
                        </div>

                        @include('layouts.partials.alert')

                        <input type="submit" class="btn btn-primary" value="{{isset($content) ? 'Update' : 'Create'}}">
                        <a href="/admin/cms" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>
            @if(isset($content))
                <div class="footer">
                    <hr>
                    <form action="/admin/cms/menu/{{$content[0]->id}}/delete" method="post">
                        {{ csrf_field() }}
                        <button class="btn btn-danger">
                            <i class="fa fa-trash-o" aria-hidden="true"></i>
                            Delete
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection

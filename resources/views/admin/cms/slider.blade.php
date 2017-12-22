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
                <h4 class="title">{{isset($content) ? 'Edit' : 'Create'}} Slider</h4>
            </div>
            <div class="content">
                <div class="row">
                    <div class="col-md-6">
                        <form action="/admin/cms/slider{{isset($content) ? '/'.$content->id : ''}}" method="post"  enctype="multipart/form-data">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <label>Image</label>
                                <input type="file" name="image">
                            </div>

                            @include('layouts.partials.alert')

                            <input type="submit" class="btn btn-primary" value="{{isset($content) ? 'Update' : 'Create'}}">
                            <a href="/admin/cms" class="btn btn-default">Cancel</a>
                        </form>
                    </div>
                    @if(isset($content))
                    <div class="col-md-6">
                        <img src="{{$content->image->getSrc()}}" width="100%">
                    </div>
                    @endif
                </div>
            </div>
            @if(isset($content))
                <div class="footer">
                    <hr>
                    <form action="/admin/cms/slider/{{$content->id}}/delete" method="post">
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

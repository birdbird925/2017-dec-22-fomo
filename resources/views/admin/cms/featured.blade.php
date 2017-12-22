@extends('layouts.admin')

@section('page-direction')
    <a href="/admin/cms">CMS</a> / Featured Product
@endsection

@section('cms-sidebar')
    active
@endsection

@section('content')
    <div class="col-sm-12">
        <div class="card">
            <div class="header">
                <h4 class="title">{{isset($content) ? 'Edit' : 'Create'}} Feature Product</h4>
            </div>
            <div class="content">
                <div class="row">
                    <form class="col-md-6" action="/admin/cms/featured{{isset($content) ? '/'.$content->id : ''}}" method="post"  enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Product</label>
                            <select name="product_id" class="form-control" id="product-dropdown">
                                @foreach($products as $product)
                                    <option value="{{$product->id}}" data-image="{{$product->image}}" {{isset($content) ? ($content->product->id == $product->id ? 'selected' : '') : '' }}>
                                        {{$product->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Background</label>
                            <input type="file" name="background">
                        </div>

                        @include('layouts.partials.alert')

                        <input type="submit" class="btn btn-primary" value="{{isset($content) ? 'Update' : 'Create'}}">
                        <a href="/admin/cms" class="btn btn-default">Cancel</a>
                    </form>
                    <div class="col-md-6 product-info">
                        <img src="{{ isset($content) ? $content->product->image : $products->first()->image}}" class="product-image" width="200">
                        @if(isset($content))
                            <img src="{{$content->image->getSrc()}}" width="100%">
                        @endif
                    </div>
                </div>
            </div>
            @if(isset($content))
                <div class="footer">
                    <hr>
                    <form action="/admin/cms/featured/{{$content->id}}/delete" method="post">
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

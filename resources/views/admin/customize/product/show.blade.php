@extends('layouts.admin')

@section('page-direction')
    <a href="/admin/customize/product">Product</a> / {{$product->name}}
@endsection

@section('customize-sidebar')
    active
@endsection

@section('customize-dropdown')
    in
@endsection

@section('product-sidebar')
    active
@endsection

@section('content')
    <div class="col-xs-12">
        <div class="card customize-product-wrapper">
            <div class="header">
                <h4 class="title">Product Info</h4>
            </div>
            <div class="content">
                <div class="row">
                    <div class="col-sm-5">
                        <div class="name">
                            {{$product->name}}
                            <div class="pull-right price">
                                $ {{$product->price}}
                            </div>
                        </div>
                        <div class="description">{!!$product->description!!}</div>
                        @if(Auth::user()->id == $product->created_by)
                            <br>
                            {{-- <a href="/customize/{{$product->id}}" class="btn btn-primary">Edit</a> --}}
                            {{-- <form action="/admin/customize/product/{{$product->id}}/delete" method="post" style="display: inline">
                                {{ csrf_field() }}
                                <input type="submit" class="btn btn-danger required-confirm" value="Delete">
                            </form> --}}
                        @endif
                        <hr>
                        <h4 class="title">Components</h4>
                        <br>
                        <ul class="components-slider" id="lightSlider">
                            @foreach(json_decode($product->images) as $image)
                                <li>
                                    <a href="{{url($image)}}" target="_blank">
                                        <img src="{{$image}}" width="50%">
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="component-control">
                            <div class="btn btn-default prev"><i class="fa fa-arrow-left"></i></div>
                            <div class="btn btn-default next"><i class="fa fa-arrow-right"></i></div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-sm-offset-1">
                        <ul class="cms-slider" id="lightSlider">
                            <li>
                                <div id="{{$product->id}}-front" class="konvas-thumb" data-thumb="{{$product->thumb}}"></div>
                            </li>
                            <li>
                                <div id="{{$product->id}}-back" class="konvas-thumb" data-thumb="{{$product->back}}"></div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

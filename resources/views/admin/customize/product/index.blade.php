@extends('layouts.admin')

@section('page-direction')
    Product
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
    <div class="col-sm-12">
        @if($products->count() == 0)
            <div class="card">
                <div class="content">
                    There are not any customize watchs yet.
                </div>
            </div>
        @else
            {{-- <div class="pull-left">
                <a href="/customize" class="btn btn-primary">Create Product</a>
            </div> --}}
            <table id="data-table" class="mdl-data-table" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr href="/admin/customize/product/{{$product->id}}">
                            <td><img src="{{$product->image}}" width="50"></td>
                            <td>{{$product->name}}</td>
                            <td>{{$product->type->name }}</td>
                            <td>$ {{$product->price}}</td>
                            <td>
                                @if($product->checkComponentStatus())
                                    <div class="hide">1</div>
                                    <i class="fa fa-check-circle"></i>
                                @else
                                    <div class="hide">0</div>
                                    <div class="fa fa-times-circle"></div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection

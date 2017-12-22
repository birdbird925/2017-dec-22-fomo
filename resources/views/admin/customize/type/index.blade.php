@extends('layouts.admin')

@section('page-direction')
    Type
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
        @if($types->count() == 0)
            <div class="card">
                <div class="content">
                    There are not any customize watchs type yet.
                </div>
            </div>
        @else
            <table id="data-table" class="mdl-data-table" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($types as $index=>$type)
                        <tr href="/admin/customize/type/{{$type->id}}">
                            <td>{{$index+1}}</td>
                            <td>{{$type->name}}</td>
                            <td>$ {{$type->price}}</td>
                            <td>{!!$type->description!!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection

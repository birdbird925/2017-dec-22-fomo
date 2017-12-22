@extends('layouts.admin')

@section('page-direction')
    Step
@endsection

@section('customize-sidebar')
    active
@endsection

@section('customize-dropdown')
    in
@endsection

@section('step-sidebar')
    active
@endsection

@section('content')
    <div class="col-sm-12">
        @if($steps->count() == 0)
            <div class="card">
                <div class="content">
                    There are not any customize steps yet.
                </div>
            </div>
        @else
            <table id="data-table" class="mdl-data-table" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Main Title</th>
                        <th>Sub Title</th>
                        <th>Direction</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($steps as $index=>$step)
                        <tr href="/admin/customize/step/{{$step->id}}">
                            <td>{{$index+1}}</td>
                            <td>{{$step->title}}</td>
                            <td>{{$step->extral_title ? $step->extral_title : '-'}}</td>
                            <td>{{$step->direction}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection

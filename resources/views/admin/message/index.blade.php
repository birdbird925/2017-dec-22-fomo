@extends('layouts.admin')

@section('page-direction')
    Message
@endsection

@section('message-sidebar')
    active
@endsection

@section('content')
    <div class="col-sm-12">
        @if(Auth::user()->notifications->count() == 0)
            <div class="card">
                <div class="content">
                    There are not any message yet.
                </div>
            </div>
        @else
            <table id="data-table" class="mdl-data-table" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Since</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(Auth::user()->notifications as $index=>$notification)
                        <tr href="/admin/message/{{$notification->id}}" class="{{$notification->read_at ? 'readed' : 'new'}}" mailto="mailto:{{$notification->data['email']}}">
                            <td>{{$index+1}}</td>
                            @foreach($notification->data as $data)
                                <td>{{$data}}</td>
                            @endforeach
                            <td>{{$notification->created_at->diffForHumans()}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection

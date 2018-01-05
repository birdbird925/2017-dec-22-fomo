@extends('layouts.admin')

@section('page-direction')
    Voucher
@endsection

@section('voucher-sidebar')
    active
@endsection

@section('content')
    <div class="col-sm-12">
        @if($vouchers->count() == 0)
            <div class="card">
                <div class="content">
                    There are not any discount voucher yet.
                </div>
            </div>
        @else
            <div class="pull-left">
                <a href="/admin/voucher/create" class="btn btn-primary">Create Voucher</a>
            </div>
            <table id="data-table" class="mdl-data-table" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Amount</th>
                        <th>Usage | Total</th>
                        <th>Date Range</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vouchers as $index=>$voucher)
                        <tr href="/admin/voucher/{{$voucher->id}}">
                            <td>{{$voucher->name}}</td>
                            <td>{{$voucher->code}}</td>
                            <td>
                              @if($voucher->type == 1)
                                {{$voucher->value}}% off
                              @elseif($voucher->type == 2)
                                $ {{$voucher->value}}
                              @else
                                Free Shipping
                              @endif
                            </td>
                            <td>{{$voucher->performance->count()}} | {{$voucher->quatity}}</td>
                            <td>
                              {{substr($voucher->start_at, 0, -9)}} to {{substr($voucher->expired_at, 0, -9)}}
                            </td>
                            <td>
                              <div class="status {{$voucher->checkStatus() == 1 ? 'warning' : ''}}">{{$voucher->checkStatus() == -1 ? 'Upcoming' : ($voucher->checkStatus() == 0 ? 'Ended' : 'Active' )}}</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection

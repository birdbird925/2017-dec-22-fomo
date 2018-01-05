@extends('layouts.admin')

@section('page-direction')
    Orders
@endsection

@section('order-sidebar')
    active
@endsection

@section('content')
    <div class="col-sm-12">
        @if($orders->count() == 0)
            <div class="card">
                <div class="content">
                    There are not any order yet.
                </div>
            </div>
        @else
            <table id="data-table" class="mdl-data-table" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Since</th>
                        <th>Customer</th>
                        <th>Country</th>
                        <th>Payment</th>
                        <th>Fulfill</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $index=>$order)
                        <tr href="/admin/order/{{$order->id}}" class="{{$order->notifications->first()->read_at != '' ? 'readed' : 'new'}}">
                            {{-- <td>{{$order->orderCode()}}</td>
                            <td>{{$order->created_at->diffForHumans()}}</td>
                            <td>
                                @if($order->user_id)
                                    <a href="/admin/customer/{{$order->user_id}}">
                                        {{$order->name}}
                                    </a>
                                @else
                                    {{$order->name}}
                                @endif
                            </td>
                            <td>{{$order->country}}</td> --}}
                            {{-- <td>
                                <span class="status {{$order->payment_status == 0 ? 'warning' : ''}}">
                                    {{$order->payment_status == 1 ? 'Paid' : 'Unpaid' }}
                                </span>
                            </td>
                            <td>
                                <span class="status {{$order->fulfillStatus() == 0 ? 'warning' : ''}}">
                                    {{$order->fulfillStatus()  ? 'Fulfilled' : 'Unfulfilled'}}
                                </span>
                            </td> --}}
                            {{-- <td>$ {{$order->subTotal() + $order->shipping_cost}}</td>
                            <td>
                                <span class="hide">{{$order->order_status}}</span>
                                <i class="fa fa-{{$order->order_status ? 'check' : 'times' }}-circle"></i>
                            </td> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection

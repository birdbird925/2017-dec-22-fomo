@extends('layouts.admin')

@section('page-direction')
    Customers
@endsection

@section('customer-sidebar')
    active
@endsection

@section('content')
    <div class="col-sm-12">
        @if($customers->count() == 0)
            <div class="card">
                <div class="content">
                    There are not any customer yet.
                </div>
            </div>
        @else
            <table id="data-table" class="mdl-data-table" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Method</th>
                        <th>Orders</th>
                        <th>Last Order</th>
                        <th>Total Spent</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $index=>$customer)
                        <tr href="/admin/customer/{{$customer->id}}">
                            <td>{{$customer->email}}</td>
                            <td>{{$customer->socialAccount ? $customer->socialAccount->provider : 'System'}}</td>
                            <td>{{$customer->order->count()}}</td>
                            <td>{{$customer->order->count() > 0 ? $customer->lastOrder()->orderCode() : '-'}}</td>
                            <td>{{$customer->order->count() > 0 ? '$ '.$customer->totalSpent() : '-'}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection

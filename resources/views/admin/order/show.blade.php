@extends('layouts.admin')

@section('page-direction')
    <a href="/admin/order">Order</a> / {{$order->orderCode()}}
@endsection

@section('order-sidebar')
    active
@endsection

@section('content')
    <div class="col-md-8">
        <div class="card">
            <div class="header">
                <h4 class="title">Item Summary</h4>
            </div>
            <div class="content">
                <div class="order-row">
                    <div class="order-header"></div>
                    <div class="order-item-summary">
                        @foreach($order->items as $item)
                            <div class="order-item">
                                <div class="product-image">
                                    <img src="{{$item->product->image}}">
                                </div>
                                <div class="product-info">
                                    <div class="name">
                                        <a href="/admin/customize/product/{{$item->product->id}}">{{$item->product->name}}</a>
                                    </div>
                                    <div class="description">
                                        {{$item->product->description}}
                                    </div>
                                </div>
                                <div class="item-info">
                                    <div class="quantity">
                                        {{$item->quantity}} pcs
                                    </div>
                                    <div class="price">
                                        $ {{$item->price * $item->quantity}}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <hr>
                <h4 class="title">
                    @if($order->order_status)
                        {{$order->fulfillStatus() ? 'Fulfillments' : 'Update order shipment'}}
                    @endif
                </h4>
                <br>
                @if($order->fulfillStatus())
                    @foreach($order->shipments as $shipment)
                    <div class="summary-row editShipment {{count($errors) > 0 ? 'hide' : ''}}">
                        <div class="summary-header">
                            <a href="{{$shipment->tracking_url}}" class="header-title">
                                {{$shipment->tracking_number}}
                            </a>
                            <div class="pull-right date">
                                {{$shipment->created_at->toDateTimeString()}}
                            </div>
                        </div>
                        <div class="item-summary">
                            <ul>
                                <li>
                                    <span>Carrier: </span>
                                    {{$shipment->shipping_carrier}}
                                </li>
                                <li>
                                    <span>Code: </span>
                                    {{$shipment->tracking_number}}
                                </li>
                                <li>
                                    <span>URL: </span>
                                    <a href="{{$shipment->tracking_url}}">
                                        {{$shipment->tracking_url}}
                                    </a>
                                </li>
                            </ul>
                        </div>
                        @if($order->order_status)
                            <button class="btn btn-primary edit-shipment-tab" data-target=".editShipment">Edit</button>
                            <form action="/admin/shipment/{{$shipment->id}}/delete" method="post" style="display: inline">
                                {{ csrf_field() }}
                                <button class="btn btn-danger required-confirm">Delete</button>
                            </form>
                        @endif
                    </div>
                    @endforeach
                    @if($order->order_status)
                        <form action="/admin/shipment/{{$shipment->id}}" method="post" class="{{count($errors) > 0 ? '' : 'hide'}} editShipment">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label>Tracking URL</label>
                                    <input type="text" name="tracking_url" class="form-control" value="{{$shipment->tracking_url}}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Tracking Code</label>
                                    <input type="text" name="tracking_code" class="form-control" value="{{$shipment->tracking_number}}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Shipment Carrier</label>
                                    <input type="text" name="shipment_carrier" class="form-control" value="{{$shipment->shipping_carrier}}">
                                </div>
                            </div>
                            @include('layouts.partials.alert')
                            <input type="submit" class="btn btn-primary" value="Update">
                            <a class="btn btn-default  edit-shipment-tab" data-target=".editShipment">Cancel</a>
                        </form>
                    @endif

                @elseif($order->order_status)
                    <form action="/admin/order/{{$order->id}}/fulfill" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label>Tracking URL</label>
                                <input type="text" name="tracking_url" class="form-control" value="{{old('tracking_url')}}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Tracking Code</label>
                                <input type="text" name="tracking_code" class="form-control" value="{{old('tracking_code')}}">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Shipment Carrier</label>
                                <input type="text" name="shipment_carrier" class="form-control" value="{{old('shipment_carrier')}}">
                            </div>
                        </div>
                        @include('layouts.partials.alert')
                        <input type="submit" class="btn btn-primary" value="Mark as fulfilled">
                    </form>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="header">
                <h4 class="title">Order info</h4>
            </div>
            <div class="content">
                <ul>
                    <li>
                        <span>Code: </span>
                        {{$order->orderCode()}}
                        <i class="fa fa-{{$order->order_status ? 'check' : 'times' }}-circle"></i>
                    </li>
                    <li>
                        <span>Date: </span>
                        {{$order->created_at->toDateTimeString()}}
                    </li>
                    <li>
                        <span>Payment:</span>
                        {{$order->payment_status == 1 ? 'Paid' : 'Unpaid' }}
                    </li>
                    <li>
                        <span>Fulfilled:</span>
                        {{$order->fulfillStatus()  ? 'Fulfilled' : 'Unfulfilled'}}
                    </li>
                    <li>
                        <span>Shipping:</span>
                        $ {{$order->shipping_cost}}
                    </li>
                    <li>
                        <span>Total</span>
                        $ {{$order->subTotal() + $order->shipping_cost}}
                    </li>
                </ul>
                <hr>
                <ul>
                    <li>
                        <span>Recipient:</span>
                        @if($order->user_id)
                            <a href="/admin/customer/{{$order->user_id}}">
                                {{$order->name}}
                            </a>
                        @else
                            {{$order->name}}
                        @endif
                    </li>
                    <li>
                        <span>Email:</span>
                        {{$order->email}}
                    </li>
                    @if($order->phone)
                        <li>
                            <span>Phone:</span>
                            {{$order->phone}}
                        </li>
                    @endif
                    @if($order->note)
                    <hr>
                    <li>
                        <span>Note:</span>
                        {{$order->note}}
                    </li>
                    @endif
                    <hr>
                    <li>
                        <span>Address:</span>
                        <br>
                        {{$order->address_line_1}}
                        <br>
                        {!!$order->address_line_2 ? $order->address_line_2.'<br>' : '' !!}
                        {{$order->postcode}}, {{$order->city}}
                        <br>
                        {{$order->state}}, {{$order->country}}
                    </li>
                </ul>
                @if($order->order_status)
                    <hr>
                    <form action="/admin/order/{{$order->id}}/cancel" method="post">
                        {{ csrf_field() }}
                        <input type="submit" value="Cancel order" class="cancel-order btn btn-danger required-confirm">
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection

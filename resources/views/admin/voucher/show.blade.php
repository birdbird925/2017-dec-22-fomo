@extends('layouts.admin')

@section('page-direction')
    <a href="/admin/voucher">Voucher</a> / {{$voucher->code}}
@endsection

@section('voucher-sidebar')
    active
@endsection

@section('content')
  @if($voucher->performance->count() == 0)
      <div class="col-sm-12">
          <div class="card">
              <div class="header">
                  <h4 class="title">Voucher Info</h4>
              </div>
              <div class="content">
                  <ul>
                      <li>
                          <span>Status: </span>
                          <i class="fa fa-{{$voucher->checkStatus() == 1 ? 'check-circle' : 'times-circle'}}"></i> {{$voucher->checkStatus() == -1 ? 'Upcoming' : ($voucher->checkStatus() == 0 ? 'Ended' : 'Active' )}}
                      </li>
                      <li>
                          <span>Name: </span>
                          {{$voucher->name}}
                      </li>
                      <li>
                          <span>Code: </span>
                          {{$voucher->code}}
                      </li>
                      <li>
                          <span>Type: </span>
                          @if($voucher->type == 1)
                            {{$voucher->value}}% off
                          @elseif($voucher->type == 2)
                            $ {{$voucher->value}}
                          @else
                            Free Shipping
                          @endif
                      </li>
                      <li>
                          <span>Quantity</span>
                          {{$voucher->quantity}}
                      </li>
                      <li>
                          <span>Date Range: </span>
                          {{substr($voucher->start_at, 0, -9)}} to {{substr($voucher->expired_at, 0, -9)}}
                      </li>
                  </ul>
                  @if($voucher->checkStatus() != 0)
                      <hr>
                      <form action="/admin/voucher/{{$voucher->id}}/delete" method="post">
                          {{ csrf_field() }}
                          <button class="btn btn-danger">
                              <i class="fa fa-trash-o" aria-hidden="true"></i>
                              Stop
                          </button>
                      </form>
                  @endif
              </div>
          </div>
      </div>
  @else
      <div class="col-md-8">
          <div class="card">
              <div class="header">
                  <h4 class="title">Voucher Usage</h4>
              </div>
              <div class="content">
                  @foreach($voucher->performance as $performance)
                      <div class="order-row summary-row">
                          <div class="summary-header">
                              <a href="/admin/order/{{$performance->order->id}}" class="header-title">
                                  <i class="fa fa-{{$performance->order->order_status == 1 ? 'check-circle' : 'times-circle'}}"></i>
                                  {{$performance->order->orderCode()}}
                              </a>

                              <div class="pull-right date">
                                  {{$performance->order->created_at->toDateTimeString()}}
                              </div>
                          </div>
                          <div class="order-item-summary item-summary">
                              @foreach($performance->order->items as $item)
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
                  @endforeach
              </div>
          </div>
      </div>
      <div class="col-sm-4">
          <div class="card">
              <div class="header">
                  <h4 class="title">Voucher Info</h4>
              </div>
              <div class="content">
                  <ul>
                      <li>
                          <span>Status: </span>
                          <i class="fa fa-{{$voucher->checkStatus() == 1 ? 'check-circle' : 'times-circle'}}"></i> {{$voucher->checkStatus() == -1 ? 'Upcoming' : ($voucher->checkStatus() == 0 ? 'Ended' : 'Active' )}}
                      </li>
                      <li>
                          <span>Name: </span>
                          {{$voucher->name}}
                      </li>
                      <li>
                          <span>Code: </span>
                          {{$voucher->code}}
                      </li>
                      <li>
                          <span>Type: </span>
                          @if($voucher->type == 1)
                            {{$voucher->value}}% off
                          @elseif($voucher->type == 2)
                            $ {{$voucher->value}}
                          @else
                            Free Shipping
                          @endif
                      </li>
                      <li>
                          <span>Usage</span>
                          {{$voucher->performance->count()}}
                      </li>
                      <li>
                          <span>Quantity</span>
                          {{$voucher->quantity}}
                      </li>
                      <li>
                          <span>Start time: </span>
                          {{substr($voucher->start_at, 0, -9)}}
                      </li>
                      <li>
                          <span>End time: </span>
                          {{substr($voucher->expired_at, 0, -9)}}
                      </li>
                  </ul>
                  @if($voucher->checkStatus() != 0)
                      <hr>
                      <form action="/admin/voucher/{{$voucher->id}}/delete" method="post">
                          {{ csrf_field() }}
                          <button class="btn btn-danger">
                              <i class="fa fa-trash-o" aria-hidden="true"></i>
                              Stop
                          </button>
                      </form>
                  @endif
              </div>
          </div>
      </div>
  @endif
@endsection

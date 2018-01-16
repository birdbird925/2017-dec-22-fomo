@extends('layouts.app')

@section('logo-class')
    fixed
@endsection

@section('footer-class')
    mobile-hide
@endsection

@section('content')
    <div class="title page-title">My account</div>
    <div id="account-wrapper">
        <div class="section-title"  href="#saved">
            <span>Saved Items</span>
        </div>
        <div id="saved" class="section collapse in">
            @if(Auth::user()->savedProduct()->count() > 0)
            <ul>
                @foreach(Auth::user()->savedProduct as $count=>$saved)
                    <a href="/customize/{{$saved->product_id}}">
                        <li id="savedProduct{{$saved->id}}" class="konvas-thumb {{$count > 1 ? 'mobile-hide' : ''}}" data-thumb="{{$saved->product->thumb}}"></li>
                    </a>
                @endforeach
            </ul>
            <a href="#" class="loadmore">Load More</a>
            <br>
            @else
                <div class="empty-msg">
                    <p>You don't have any saved item yet.</p>
                    <a href="/customize">Start Shopping</a>
                </div>
            @endif
        </div>
        <div class="section-title" href="#order">
            <span>My order</span>
        </div>
        <div id="order" class="section collapse">
            @if(Auth::user()->order()->count() > 0)
                <table class="product-table table">
                    @foreach(Auth::user()->order as $order)
                        @foreach($order->items as $item)
                            <tr>
                                <td class="image-col">
                                    <div id="order{{$order->id}}" class="konvas-thumb" data-thumb="{{$item->product->thumb}}"></div>
                                </td>
                                <td class="description-col col-md-4">
                                    <div class="name">{{$item->product->name}}</div>
                                    <div class="description">{{$item->product->description}}</div>
                                </td>
                                <td class="price-col">
                                    $ {{$item->product->price}}
                                </td>
                                <td class="date-col">
                                    {{substr($order->created_at, 0, 10)}}
                                </td>
                                <td class="code-col">
                                    {{$order->orderCode()}}
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </table>
            @else
                <div class="empty-msg">
                    <p>You don't have any saved item yet.</p>
                    <a href="/customize">Start Shopping</a>
                </div>
            @endif

        </div>
        <div class="section-title" data-toggle="collapse" href="#account">
            <span>My details</span>
        </div>
        <div id="account" class="section collapse">
            <div class="col-sm-4" id="account-info">
                <div class="form-group">
                    <label>
                        My email
                        <span class="pull-right editEmail">Update email</span>
                    </label>
                    <input type="email" class="form-control" value="{{Auth::user()->email}}" disabled>
                </div>
                <div class="form-group">
                    <label>
                        Password
                        <span class="pull-right" class="editPassword">Change password</span>
                    </label>
                    <input type="password" class="form-control" value="dummypassword" disabled>
                </div>
            </div>
            <div class="col-sm-4" id="email-form">
                <form action="/account/email" method="post">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>Old email</label>
                        <input type="email" class="form-control" value="{{Auth::user()->email}}" disabled>
                    </div>
                    <div class="form-group">
                        <label>New email</label>
                        <input type="email" name="email" class="form-control">
                    </div>
                    <input type="submit" value="save" data-action="email">
                </form>
                <br>
                <a class="cancelEdit">Cancel</a>
            </div>
            <div class="col-sm-4" id="password-form">
                <form action="/account/password" method="post">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>Old password</label>
                        <input type="password" name="old-password" class="form-control">
                        <a class="show-password">show</a>
                    </div>
                    <div class="form-group">
                        <label>New password</label>
                        <input type="password" name="new-password" class="form-control">
                        <a class="show-password">show</a>
                    </div>
                    <input type="submit" value="save" data-action='password'>
                </form>
                <br>
                <a class="cancelEdit">Cancel</a>
            </div>
            <br>
        </div>
    </div>
@endsection

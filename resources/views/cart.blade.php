@extends('layouts.app')

@section('logo-class')
    fixed
@endsection

@section('footer-class')
    {{sizeof(Session::get('cart.item')) > 0 ? 'hide' : ''}}
    mobile-hide
@endsection

@section('content')
    <div class="cart-wrapper">
        <div class="cart-body">
            <div class="title page-title">Your Cart</div>

            @if(sizeof(Session::get('cart.item')) == 0)
                <div class="empty-msg">
                    <p>Oops! There is nothing in your cart yet.</p>
                    <a href="/customize/">Build your first watch</a>
                </div>
            @else
                <table class="product-table table">
                    @foreach(Session::get('cart.item') as $index=>$item)
                        <tr>
                            <td class="image-col" align="center">
                                <div id="{{$item['code']}}" class="konvas-thumb" data-thumb="{{$item['thumb']}}"></div>
                            </td>
                            <td class="description-col col-md-4">
                                <div class="name">{{$item['name']}}</div>
                                <div class="description">{{$item['description']}}</div>
                            </td>
                            <td class="quantity-col">
                                <select name="quantity" data-id="{{$index}}" class="quantity-dropdown">
                                  <option value="1" {{$item['quantity'] == 1 ? 'selected' : ''}}>1 piece</option>
                                  <option value="2" {{$item['quantity'] == 2 ? 'selected' : ''}}>2 piece</option>
                                  <option value="3" {{$item['quantity'] == 3 ? 'selected' : ''}}>3 piece</option>
                                  <option value="4" {{$item['quantity'] == 4 ? 'selected' : ''}}>4 piece</option>
                                  <option value="5" {{$item['quantity'] == 5 ? 'selected' : ''}}>5 piece</option>
                                  <option value="6" {{$item['quantity'] == 6 ? 'selected' : ''}}>6 piece</option>
                                  <option value="7" {{$item['quantity'] == 7 ? 'selected' : ''}}>7 piece</option>
                                  <option value="8" {{$item['quantity'] == 8 ? 'selected' : ''}}>8 piece</option>
                                  <option value="9" {{$item['quantity'] == 9 ? 'selected' : ''}}>9 piece</option>
                                </select>
                                <span class="quantity">{{$item['quantity']}} piece</span>
                            </td>
                            <td class="price-col">
                                {{session('currency')}} {{$item[session('currency').'_price']}}
                            </td>
                            <td class="control-col">
                                <a href="#" class="edit" data-id="{{$index}}">Edit</a>
                                <form action="/cart/{{$index}}/remove" method="post">
                                    {{ csrf_field() }}
                                    <button type="submit" class="remove"></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tabel>
                <table></table>
            @endif
        </div>
        @if(sizeof(Session::get('cart.item')) != 0)
          <div class="cart-footer">
              <div class="caption">TOTAL</div>
              <div class="total">
                  {{session('currency')}} <span>{{Session::get('cart.total')}}</span>
              </div>
              <a id="checkout-button" class="checkout" href="/checkout">Proceed to checkout</a>
          </div>
        @endif
    </div>

@endsection

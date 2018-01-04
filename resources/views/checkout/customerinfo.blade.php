@extends('layouts.app')

@section('logo-class')
    fixed
@endsection

@section('content')
    <div class="checkout-wrapper">
        <div class="title page-title">CHECK OUT</div>
        <div class="container">
          <div class="row">
            <div class="col-md-3 col-md-push-7 col-xs-10 col-xs-offset-1">
              <table class="checkout-item-table table">
                @foreach(Session::get('cart.item') as $index=>$item)
                  <tr>
                    <td class="image-col">
                      <div id="{{$item['code']}}" class="konvas-thumb" data-thumb="{{$item['thumb']}}"></div>
                    </td>
                    <td class="description-col">
                      <div class="name">{{$item['name']}}</div>
                      {{$item['quantity']}} piece
                    </td>
                    <td class="price-col">
                      $ {{$item['price']}}
                    </td>
                  </tr>
                @endforeach
                  <tr class="small-padding-row">
                    <td class="image-col"></td>
                    <td class="top-border description-col">Subtotal</td>
                    <td class="top-border price-col">$ {{Session::get('cart.total')}}</td>
                  </tr>
                  <tr class="small-padding-row">
                    <td class="image-col"></td>
                    <td class="description-col">Shipping</td>
                    <td class="price-col">$ {{Session::get('cart.shipping.cost')}}</td>
                  </tr>
                  @if(Session::get('checkout.voucher'))
                    <tr class="small-padding-row">
                      <td class="image-col"></td>
                      <td class="description-col">Discount</td>
                      <td class="price-col">-$ {{Session::get('checkout.voucher.value')}}</td>
                    </tr>
                  @endif
                  <tr class="voucher-row">
                    <td class="image-col"></td>
                    <td>
                      <form action="/discount-code/apply" method="post">
                        {{ csrf_field() }}
                        <input type="text" name="voucher" placeholder="Discount Code" autocomplete="off">
                        <input type="submit" value="Apply">
                      </form>
                    </td>
                    <td></td>
                  </tr>
                  <tr>
                    <td class="image-col"></td>
                    <td class="top-border description-col">TOTAL</td>
                    <td class="top-border price-col">$ {{Session::get('cart.total') + Session::get('cart.shipping.cost') - Session::get('checkout.voucher.value')}}</td>
                  </tr>
              </table>
            </div>
            <div class="col-md-6 col-md-pull-4 col-xs-10 col-xs-offset-1">
              <div class="subtitle">Customer Details</div>
              <form action="/checkout/shipping/save" method="post" name="customerDetail" data-reload="{{Session::has('checkout.shipping.email') ? 'no' : 'yes'}}">
                {{ csrf_field() }}
                <div class="form-group">
                  <label for="email">Email
                    @if(!Auth::check())
                      <div class="stick-right-form-control">
                        Already have account? <a class="login-tab">login</a>
                      </div>
                    @endif
                  </label>
                  <input id="email" type="email" name="email" placeholder="Email Address" value="{{ Session::has('checkout.shipping.email') ? Session::get('checkout.shipping.email') : (Auth::check() ? Auth::user()->email : '') }}" autocomplete="off">
                </div>
                <div class="subtitle shipping-detail">Shipping Details</div>
                <div class="form-group half">
                  <label for="firstName">First Name</label>
                  <input id="firstName" type="text" name="firstName" placeholder="First Name" autocomplete="off" value="{{Session::has('checkout.shipping.firstName') ? Session::get('checkout.shipping.firstName') : ''}}">
                </div>
                <div class="form-group half">
                  <label for="lastName">Last Name</label>
                  <input id="lastName" type="text" name="lastName" placeholder="Last Name / Family Name" autocomplete="off" value="{{Session::has('checkout.shipping.lastName') ? Session::get('checkout.shipping.lastName') : ''}}">
                </div>
                <div class="form-group">
                  <label for="apartment">Apt, Suite etc (optional)</label>
                  <input id="apartment" type="text" name="apartment" placeholder="Apartment / Suite / etc" autocomplete="off" value="{{Session::has('checkout.shipping.apartment') ? Session::get('checkout.shipping.apartment') : ''}}">
                </div>
                <div class="form-group">
                  <label for="address">Address</label>
                  <input id="address" type="text" name="address" placeholder="Address" autocomplete="off" value="{{Session::has('checkout.shipping.address') ? Session::get('checkout.shipping.address') : ''}}">
                </div>
                <div class="form-group half">
                  <label for="city">City</label>
                  <input id="city" type="text" name="city" placeholder="City" autocomplete="off" value="{{Session::has('checkout.shipping.city') ? Session::get('checkout.shipping.city') : ''}}">
                </div>
                <div class="form-group half">
                  <label for="postal">postal Code</label>
                  <input id="postal" type="text" name="postal" placeholder="Postal Code" autocomplete="off" value="{{Session::has('checkout.shipping.postal') ? Session::get('checkout.shipping.postal') : ''}}">
                </div>
                <div class="form-group half">
                  <label for="country">Country</label>
                  <select id="country" name="country" placeholder="Country" data-value="{{Session::has('checkout.shipping.country') ? Session::get('checkout.shipping.country') : ''}}"></select>
                </div>
                <div class="form-group half">
                  <label for="state">State</label>
                  <select name="state" id="state" placeholder="State" data-value="{{Session::has('checkout.shipping.state') ? Session::get('checkout.shipping.state') : ''}}"></select>
                </div>
                <div class="form-group">
                  <label for="contact">Contact Number</label>
                  <input id="contact" type="text" name="contact" placeholder="(Country Code)(Contact Number)" autocomplete="off" value="{{Session::has('checkout.shipping.contact') ? Session::get('checkout.shipping.contact') : ''}}">
                </div>
              </form>
              <div class="checkout-navigation">
                <a href="/cart">Return to Cart</a>
                {{-- <form action="/checkout" method="post" style="display: inline;">
                  {{ csrf_field() }}
                  <input class="paypal" type="submit" value="PAYPAL PAYMENT">
                </form> --}}
                <div class="paypal-button-wrapper">
                  <div id="paypal-button-container"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
@endsection


@push('scripts')
  <script src="/js/countries.js"></script>
  <script language="javascript">
  	populateCountries("country", "state"); // first parameter is id of country drop-down and second parameter is id of state drop-down
    if($('#country').attr('data-value') != '') {
      $('#country').val($('#country').attr('data-value'));
      populateStates('country', 'state');
      $('#state').val($('#state').attr('data-value'));
    }

    function isValid() {
      var inputField = ['email', 'firstName', 'lastName', 'address', 'city', 'postal', 'country', 'state', 'contact'];
      var hasEmpty = false;

      $.each(inputField, function(index, value) {
        if($('#'+value).val() == '') {
          hasEmpty = true;
        }
      });
      return !hasEmpty;
    }

    function onChangeInput(handler) {
      var inputField = ['email', 'firstName', 'lastName', 'address', 'city', 'postal', 'country', 'state', 'contact'];
      $.each(inputField, function(index, value) {
        $('#'+value).on('change', handler);
      });
    }

    function toggleButton(actions) {
        return isValid() ? actions.enable() : actions.disable();
    }

    paypal.Button.render({

        env: 'sandbox', // sandbox | production

        client: {
            sandbox:    'AZDxjDScFpQtjWTOUtWKbyN_bDt4OgqaF4eYXlewfBP4-8aqX3PiV8e1GWU6liB2CUXlkA59kJXE7M6R',
            production: '<insert production client id>'
        },
        validate: function(actions) {
            toggleButton(actions);
            onChangeInput(function() {
                toggleButton(actions);
            });
        },
        onClick: function() {
            if(!isValid()) {
              $('.msg-popup').find('.title').html('Erm');
              $('.msg-popup').find('.caption').html('Don\'t fill up the form is not cool!');
              $('.msg-popup').toggleClass('popup');
              setTimeout(function(){ $('.msg-popup').toggleClass('popup'); }, 2000);
            }
            // else {
              // var data = $('form[name=customerDetail]').serialize();
              // $.post('/checkout/shipping/save', $('form[name=customerDetail]').serialize(), function(res){
              //   console.log('saved');
              // });
            // }
        },
        style: {
            label: 'paypal',
            size:  'medium',    // small | medium | large | responsive
            shape: 'rect',     // pill | rect
            color: 'blue',     // gold | blue | silver | black
            tagline: false
        },

        // payment() is called when the button is clicked
        payment: function() {
            console.log('start paypal page');

            // Set up a url on your server to create the payment
            var CREATE_URL = '/checkout/paypal/payment/create';

            // Make a call to your server to set up the payment
            return paypal.request.post(CREATE_URL, {_token: $('input[name="_token"]')})
                .then(function(res) {
                    // return res.paymentID;
                    return res;
                });
        },

        // onAuthorize() is called when the buyer approves the payment
        onAuthorize: function(data, actions) {

            // Set up a url on your server to execute the payment
            var EXECUTE_URL = '/checkout/done';

            // Set up the data you need to pass to your server
            var data = $('form[name=customerDetail]').serialize();
            data.push({
                paymentID: data.paymentID,
                payerID: data.payerID
            });

            var EXECUTE_URL = '/checkout/done';

            // Make a call to your server to execute the payment
            return paypal.request.post(EXECUTE_URL, data)
                .then(function (res) {
                    if(res == 'cart') {
                      window.location.href = "/cart";
                    }
                    else if(res == 'account') {
                      window.location.href = '/account';
                    }
                    else {
                      console.log(res);
                      // window.location.reload();
                    }
                });
        }

    }, '#paypal-button-container');
  </script>
@endpush

@push('head-scripts')
  <script src="https://www.paypalobjects.com/api/checkout.js"></script>
@endpush

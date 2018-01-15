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
                  <tr class="small-padding-row" id="discount-row" style="display: none;">
                    <td class="image-col"></td>
                    <td class="description-col">Discount</td>
                    <td class="price-col"></td>
                  </tr>
                  <tr class="voucher-row">
                    <td class="image-col"></td>
                    <td>
                      <form action="/discount-code/apply" method="post" name="voucherForm">
                        {{ csrf_field() }}
                        <input id="voucher" type="text" name="voucher" placeholder="Discount Code" autocomplete="off">
                        <input type="submit" value="Apply">
                      </form>
                    </td>
                    <td></td>
                  </tr>
                  <tr id="total-row">
                    <td class="image-col"></td>
                    <td class="top-border description-col">TOTAL</td>
                    <td class="top-border price-col" default-total-amount="{{Session::get('cart.total') + Session::get('cart.shipping.cost')}}">$ {{Session::get('cart.total') + Session::get('cart.shipping.cost')}}</td>
                  </tr>
                  <tr class=note-row>
                    <td class="image-col"></td>
                    <td>
                      <div class="note-wrapper">
                        <textarea name="note" id="note-txtbox" rows="5" placeholder="Let us know if you would like any further personalization (* Extra charges will be added.)"></textarea>
                      </div>
                    </td>
                    <td></td>
                  </tr>
              </table>
            </div>
            <div class="col-md-6 col-md-pull-4 col-xs-10 col-xs-offset-1">
              <div class="subtitle">Customer Details</div>
              <form action="/checkout/shipping/save" method="post" name="customerDetail">
                {{ csrf_field() }}
                <div class="form-group">
                  <label for="email">Email
                    @if(!Auth::check())
                      <div class="stick-right-form-control">
                        Already have account? <a class="login-tab">login</a>
                      </div>
                    @endif
                  </label>
                  <input id="email" type="email" name="email" placeholder="Email Address" value="{{ Auth::check() ? Auth::user()->email : '' }}" autocomplete="off">
                </div>
                <div class="subtitle shipping-detail">Shipping Details</div>
                <div class="form-group half">
                  <label for="firstName">First Name</label>
                  <input id="firstName" type="text" name="firstName" placeholder="First Name" autocomplete="off">
                </div>
                <div class="form-group half">
                  <label for="lastName">Last Name</label>
                  <input id="lastName" type="text" name="lastName" placeholder="Last Name / Family Name" autocomplete="off">
                </div>
                <div class="form-group">
                  <label for="apartment">Apt, Suite etc (optional)</label>
                  <input id="apartment" type="text" name="apartment" placeholder="Apartment / Suite / etc" autocomplete="off">
                </div>
                <div class="form-group">
                  <label for="address">Address</label>
                  <input id="address" type="text" name="address" placeholder="Address" autocomplete="off">
                </div>
                <div class="form-group half">
                  <label for="city">City</label>
                  <input id="city" type="text" name="city" placeholder="City" autocomplete="off">
                </div>
                <div class="form-group half">
                  <label for="postal">postal Code</label>
                  <input id="postal" type="text" name="postal" placeholder="Postal Code" autocomplete="off">
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
                  <input id="contact" type="text" name="contact" placeholder="(Country Code)(Contact Number)" autocomplete="off">
                </div>
              </form>
              <div class="checkout-navigation">
                <a href="/cart">Return to Cart</a>
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

    // verify voucher
    $('form[name=voucherForm]').on('submit', function(e){
      e.preventDefault();
      var data = $(this).serialize();
      $.ajax({
        url: "/discount-code/apply",
        type: 'post',
        data: data,
        dataType: 'json',
        success: function(res){
          if(res.error) {
            $('#voucher').val('');
            var total = $('#total-row .price-col').attr('default-total-amount');
            $('#total-row .price-col').html('$ '+total);
            $('#discount-row').css("display", "none");
            $('.msg-popup').find('.title').html('Opps');
            $('.msg-popup').find('.caption').html(res.message);
            $('.msg-popup').toggleClass('popup');
            setTimeout(function(){ $('.msg-popup').toggleClass('popup'); }, 2000);
          }
          else {
            //display voucher discount amount
            $('#discount-row .price-col').html('-$ '+res.amount);
            $('#total-row .price-col').html('$ '+res.total);
            $('#discount-row').css("display", "table-row");
          }
        }
      });
    });

    function isValid(requireAlert = false) {
      // check voucher code
      if($('#voucher').val() != '') {
        var data = $('form[name=voucherForm]').serialize();
        var hasError = false;
        $.ajax({
          url: "/discount-code/apply",
          type: 'post',
          data: data,
          dataType: 'json',
          async: false,
          success: function(res){
            if(res.error){
                if(requireAlert) {
                    $('#voucher').addClass('animated shake error');
                    setTimeout(function() { $('#voucher').removeClass('animated shake'); }, 1000);
                }
              hasError = res.message;
            }
            else {
                $('#voucher').removeClass('error');
            }
          }
        });
        if(hasError !== false) {
          return hasError;
        }
      }

      var inputField = ['email', 'firstName', 'lastName', 'address', 'city', 'postal', 'country', 'state', 'contact'];

      var hasEmpty = false;
      $.each(inputField, function(index, value) {
        var inputValue = $('#'+value).val()
        if(inputValue == '' || inputValue == -1 || inputValue == null) {
            if(requireAlert) {
                $('#'+value).addClass('animated shake error');
                setTimeout(function() { $('#'+value).removeClass('animated shake'); }, 1000);
            }
            hasEmpty = true;
        }
        else {
            $('#'+value).removeClass('error');
        }
      });
      if(hasEmpty) { return 'Don\'t fill up the form is not cool!'}

      // check email format
      var email_regex=/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
      if(email_regex.test($('#email').val())==false){
          if(requireAlert) {
              $('#email').addClass('animated shake error');
              setTimeout(function() { $('#email').removeClass('animated shake'); }, 1000);
          }
        return 'Email address format is wrong!';
      }
      else {
          $('#email').removeClass('error');
      }

      return true;
    }

    function onChangeInput(handler) {
      var inputField = ['email', 'firstName', 'lastName', 'address', 'city', 'postal', 'country', 'state', 'contact', 'voucher'];
      $.each(inputField, function(index, value) {
        $('#'+value).on('change', handler);
      });
    }

    function toggleButton(actions) {
      return isValid() === true ? actions.enable() : actions.disable();
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
          if(isValid(true) !== true) {
            $('.msg-popup').find('.title').html('Erm');
            $('.msg-popup').find('.caption').html(isValid(true));
            $('.msg-popup').toggleClass('popup');
            setTimeout(function(){ $('.msg-popup').toggleClass('popup'); }, 2000);s
          }
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
            // Set up a url on your server to create the payment
            var CREATE_URL = '/checkout/paypal/payment/create';

            // Make a call to your server to set up the payment
            return paypal.request.post(CREATE_URL, {'_token': $('input[name="_token"]').val(), 'voucher': $('#voucher').val()})
                .then(function(res) {
                    // return res.paymentID;
                    return res;
                });
        },

        // onAuthorize() is called when the buyer approves the payment
        onAuthorize: function(data, actions) {
            // Set up a url on your server to execute the payment
            // var EXECUTE_URL = '/checkout/paypal/payment/execute';
            var EXECUTE_URL = '/checkout/done';

            // Set up the data you need to pass to your server
            var data = {
                paymentID: data.paymentID,
                payerID: data.payerID,
                _token: $('input[name="_token"]').val(),
                email: $('#email').val(),
                firstName: $('#firstName').val(),
                lastName: $('#lastName').val(),
                apartment: $('#apartment').val(),
                address: $('#address').val(),
                city: $('#city').val(),
                postal: $('#postal').val(),
                country: $('#country').val(),
                state: $('#state').val(),
                contact: $('#contact').val(),
                voucher: $('#voucher').val(),
                note: $('#note-txtbox').val()
            };

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
        },
        onError: function(err) {
            console.log(err);
        }

    }, '#paypal-button-container');
  </script>
@endpush

@push('head-scripts')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
  <script src="https://www.paypalobjects.com/api/checkout.js"></script>
@endpush

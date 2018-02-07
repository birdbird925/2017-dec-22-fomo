<?php

namespace App\Http\Controllers;

use App\Repositories\Image\ImageRepository;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Notifications\OrderSuccess;
use App\Mail\NewOrderNotificationEmail;
use Swap\Laravel\Facades\Swap;
use Carbon\Carbon;
use App\CustomizeProduct;
use App\Order;
use App\OrderItem;
use App\Voucher;
use App\VoucherHistory;
use Paypal;
use Redirect;

class CheckoutController extends Controller
{
    private $_apiContext;

    public function __construct()
    {
        $this->_apiContext = PayPal::ApiContext(
            config('services.paypal.client_id'),
            config('services.paypal.secret'));

    		$this->_apiContext->setConfig(array(
    			'mode' => config("services.paypal.model"),
    			'service.EndPoint' => config("services.paypal.endpoint"),
    			'http.ConnectionTimeOut' => 30,
    			'log.LogEnabled' => true,
    			'log.FileName' => storage_path('logs/paypal.log'),
    			'log.LogLevel' => 'FINE',
                'validate_ssl'   => true,
    		));

    }

    public function validation()
    {
        if(sizeof(session('cart.item')) == 0)
            return 'empty';
        // validation pass, add product image to session
        $image = json_decode(request('image'));
        $count = 0;
        foreach(session('cart.item') as $index=>$item) {
            session(["cart.item.$index.image" => $image[$count]]);
            $count++;
        }
        return 'true';
    }

    public function checkout(Request $request)
    {
        return view('checkout.customerinfo');
    }

    // verify voucher
    public function submitVoucher(Request $request)
    {
        if($request->voucher == null) {
          return Response::json(['error' => true,'message' => 'Discount code can\'t be empty.'], 200);
        }

        $voucher = Voucher::where('code', $request->voucher)->first();
        if(!$voucher) {
          return Response::json(['error' => true,'message' => 'Discount code is not valid.'], 200);
        }

        if($voucher->start_at != null) {
            $now = Carbon::now();
            $start_at = Carbon::parse($voucher->start_at);
            $expired_at = Carbon::parse($voucher->expired_at);

            if($now->lt($start_at)) {
              return Response::json(['error' => true,'message' => 'Discount code is not valid.'], 200);
            }

            if($expired_at->lt($now)) {
              return Response::json(['error' => true,'message' => 'Discount code is expired!'], 200);
            }
        }

        if($voucher->quantity) {
            if($voucher->performance->count() >= $voucher->quantity){
                return Response::json(['error' => true,'message' => 'Discount code has been fully redeemed!'], 200);
            }
        }

        switch($voucher->type) {
          case 1:
          $amount = session('cart.total') * $voucher->value / 100;
          break;

          case 2:
            $rate = 1;
            if(session('currency') != 'USD') {
              $rate = Swap::latest('USD/'.session('currency'))->getValue();
            }
            $discount = number_format((float)($voucher->value * $rate), 2, '.', '');
            break;

          case 3:
            $discount = session('cart.shipping.cost');
            break;

        }

        return Response::json([
          'error' => false,
          'amount' => $amount,
          'total' => session('cart.total') - $amount
        ], 200);
    }

    public function paypal(Request $request)
    {
    	$payer = PayPal::Payer();
    	$payer->setPaymentMethod('paypal');

      $detail = PayPal::Details();
      $detail->setSubtotal(session('cart.total'));
      $detail->setShipping(0);

    	$amount = PayPal::Amount();
      $amount->setCurrency(session('currency'));

      $total = session('cart.total');

      // discount
      if($request->get('voucher') != '') {
        $voucher = Voucher::where('code', $request->voucher)->first();
        if($voucher->type == 1) {
            $discount = session('cart.total') * $voucher->value / 100;
        }
        $detail->setShippingDiscount(-$discount);
        $total -= $discount;
      }
    	$amount->setTotal($total);
      $amount->setDetails($detail);

      $items = PayPal::ItemList();
      foreach(session('cart.item') as $cartItem) {
          $item = PayPal::Item();
          $item->setSku($cartItem['code']);
          $item->setName($cartItem['name']);
          $item->setDescription($cartItem['description']);
          $item->setQuantity($cartItem['quantity']);
          $item->setCurrency(session('currency'));
          $item->setPrice($cartItem[session('currency').'_price']);
          $items->addItem($item);
      }

      $transaction = PayPal::Transaction();
    	$transaction->setAmount($amount);
      $transaction->setDescription('Creating a payment');
      $transaction->setItemList($items);

    	$redirectUrls = PayPal:: RedirectUrls();
    	$redirectUrls->setReturnUrl(action('CheckoutController@getDone'));
    	$redirectUrls->setCancelUrl(action('CartController@index'));

    	$payment = PayPal::Payment();
    	$payment->setIntent('sale');
    	$payment->setPayer($payer);
    	$payment->setRedirectUrls($redirectUrls);
    	$payment->setTransactions(array($transaction));
      $payment->setExperienceProfileId($this->createWebProfile());
      try {
          $response = $payment->create($this->_apiContext);
          return $response->id;
      } catch (PayPal\Exception\PayPalConnectionException $pce) {
          return json_decode($pce->getData());
          // echo '<pre>';print_r(json_decode($pce->getData()));exit;
      }
    }

    public function getDone(Request $request)
    {
    	$id = $request->get('paymentID');
    	$payer_id = $request->get('payerID');
      $payment = PayPal::getById($id, $this->_apiContext);
      $payerInfo = $payment->getPayer()->getPayerInfo();
      $transaction = $payment->getTransactions()[0];
      $amount = $transaction->getAmount();
      $items = $transaction->getItemList()->getItems();
      $paymentExecution = PayPal::PaymentExecution();
      $paymentExecution->setPayerId($payer_id);

      try {
          $executePayment = $payment->execute($paymentExecution, $this->_apiContext);
          $transactionID = $executePayment->getTransactions()[0]->getRelatedResources()[0]->getSale()->getId();
          if($executePayment->getState() == 'approved') {
              $order = Order::create([
                  'user_id' => Auth::check() ? Auth::user()->id : null,
                  'name' => $request->get('firstName').' '.$request->get('lastName'),
                  'email' => $request->get('email'),
                  'phone' => $request->get('contact'),
                  'address_line_1' => $request->get('apartment'),
                  'address_line_2' => $request->get('address'),
                  'city' => $request->get('city'),
                  'postcode' => $request->get('postal'),
                  'state' => $request->get('state'),
                  'country' => $request->get('country'),
                  'shipping_cost' => 0,
                  'currency' => session('currency'),
                  'currency_rate' => session('currencyRate'),
                  'note' => $request->get('note'),
                  // 'paypal_id' => $payment->getId(),
                  'paypal_id' => $transactionID,
                  'payment_status' => 1
              ]);

              $cartItemCode = [];
              foreach($items as $item)
                  $cartItemCode[] = $item->sku;

              foreach(session('cart.item') as $cartItem) {
                  if(in_array($cartItem['code'], $cartItemCode)) {
                      $component = json_decode($cartItem['product']);
                      $type_id = $component->customize_type->value;
                      $product = CustomizeProduct::create([
                          'name' => $cartItem['name'],
                          'components' => $cartItem['product'],
                          // 'image' => $cartItem['image'],
                          'images' =>$cartItem['images'],
                          'thumb' => $cartItem['thumb'],
                          'back' => $cartItem['back'],
                          'type_id' => $type_id,
                          'description' => $cartItem['description'],
                          'price' => $cartItem[session('currency').'_price'],
                          'created_by' => Auth::check() ? Auth::user()->id : null,
                      ]);

                      $key = array_search($cartItem['code'],$cartItemCode);
                      $orderItem = $order->items()->create([
                          'product_id' => $product->id,
                          'price' => $items[$key]->price,
                          'quantity' => $items[$key]->quantity
                      ]);
                      $order->save();
                  }
              }

              if($request->get('voucher') != '') {
                $voucher = Voucher::where('code', $request->voucher)->first();
                if($voucher->type == 1) {
                    $discount = session('cart.total') * $voucher->value / 100;
                }
                $voucherHistory = VoucherHistory::create([
                    'voucher_id' => $voucher->id,
                    'order_id' => $order->id,
                    'email' => $request->get('email'),
                    'amount' => $discount,
                ]);
              }


              // remove session cart
              session()->forget("cart");
              // send mail
              $order->notify(new OrderSuccess($order));
              Mail::to('kris@fomo.watch')->send(new NewOrderNotificationEmail());

              session()->flash('popup', [
                  'title' => 'Hooray!',
                  'caption' => 'You order is successfully placed.'
              ]);

              if(Auth::check())
                  return 'account';
                  // return redirect('/account');
              else
                  return 'cart';
                  // return redirect('/cart');
          }
          else {
              session()->flash('popup', [
                  'title' => 'Ermm',
                  'caption' => 'Fail to process the order, please try again.'
              ]);
              // return redirect('/cart');
              return 'checkout';
          }
      } catch (PayPal\Exception\PayPalConnectionException $pce) {
          echo '<pre>';print_r(json_decode($pce->getData()));exit;
      }
    }

    public function createWebProfile(){
    	$flowConfig = PayPal::FlowConfig();
      $flowConfig->setLandingPageType("Billing");
      // $flowConfig->setUserAction('commit'); // user_action=commit
    	$inputFields = PayPal::InputFields();
      $inputFields->setNoShipping(1);
      $presentation = PayPal::Presentation();
      // $presentation->setLogoImage("https://fomo.watch/images/demo/paypal-logo.svg")->setBrandName("FOMO"); //NB: Paypal recommended to use https for the logo's address and the size set to 190x60.
    	$webProfile = PayPal::WebProfile();

    	$webProfile->setName("FOMO".uniqid())
          // ->setTemporary('false') // if cant then delete
      		->setFlowConfig($flowConfig)
      		// Parameters for style and presentation.
      		->setPresentation($presentation)
      		// Parameters for input field customization.
      		->setInputFields($inputFields);

    	$createProfileResponse = $webProfile->create($this->_apiContext);

    	return $createProfileResponse->getId(); //The new webprofile's id
    }
}

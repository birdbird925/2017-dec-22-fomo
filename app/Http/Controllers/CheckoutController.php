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
use Swap\Laravel\Facades\Swap;
use Carbon\Carbon;
use App\CustomizeProduct;
use App\Order;
use App\OrderItem;
use App\Voucher;
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
    			'mode' => 'sandbox',
    			'service.EndPoint' => 'https://api.sandbox.paypal.com',
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
        if(session('cart.shipping.location') == '')
            return 'shipping';
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
      if(sizeof(session('cart.item')) == 0 || session('cart.shipping.location') == '')
          return redirect()->back();
      else
          return view('checkout.customerinfo');
    }

    public function submitVoucher(Request $request)
    {

        $hasError = false;
        if($request->voucher == null) {
          session()->flash('popup', [
              'title' => 'Opps!',
              'caption' => 'Discount code can\'t be empty'
          ]);
          $hasError = true;
        }

        $voucher = Voucher::where('code', $request->voucher)->first();

        if(!$voucher) {
          session()->flash('popup', [
              'title' => 'Opps!',
              'caption' => 'Discount code is not valid'
          ]);
          return redirect()->back();
        }

        if($voucher->start_at != null) {
            $now = Carbon::now();
            $start_at = Carbon::parse($voucher->start_at);
            $expired_at = Carbon::parse($voucher->expired_at);

            if($now->lt($start_at)) {
              session()->flash('popup', [
                  'title' => 'Opps!',
                  'caption' => 'Discount code is not valid'
              ]);

              $hasError = true;
            }

            if($expired_at->lt($now)) {
              session()->flash('popup', [
                  'title' => 'Opps!',
                  'caption' => 'Discount code is expired!'
              ]);

              $hasError = true;
            }
        }

        if($voucher->quatity) {
            if($voucher->performance->count() >= $voucher->quatity){
                session()->flash('popup', [
                    'title' => 'Opps!',
                    'caption' => 'Discount code has been fully redeemed!'
                ]);

                $hasError = true;
            }
        }

        if(!$hasError) {
          if($voucher->type == 1) {
            $amount = session('cart.total') * $voucher->value / 100;
          }
          if($voucher->type == 2) {
            $rate = 1;
            if(session('currency') != 'USD') {
              $rate = Swap::latest('USD/'.session('currency'))->getValue();
            }
            $amount = number_format((float)($voucher->value * $rate), 2, '.', '');
          }
          if($voucher->type == 3) {
            $amount = session('cart.shipping.cost');
          }
          session([
              'checkout.voucher.id'=>$voucher->id,
              'checkout.voucher.code'=>$voucher->code,
              'checkout.voucher.value'=>$amount
          ]);
        }
        return redirect()->back();
    }

    public function saveShipping(Request $request)
    {
      // session([
      //     'checkout.shipping.email'=>$request->email,
      //     'checkout.shipping.firstName'=>$request->firstName,
      //     'checkout.shipping.lastName'=>$request->lastName,
      //     'checkout.shipping.apartment'=>$request->apartment,
      //     'checkout.shipping.address'=>$request->address,
      //     'checkout.shipping.city'=>$request->city,
      //     'checkout.shipping.postal'=>$request->postal,
      //     'checkout.shipping.country'=>$request->country,
      //     'checkout.shipping.state'=>$request->state,
      //     'checkout.shipping.contact'=>$request->contact
      // ]);

      $request->session()->put('checkout.shipping.email', $request->email);
      $request->session()->put('checkout.shipping.firstName', $request->firstName);
      $request->session()->put('checkout.shipping.lastName', $request->lastName);
      $request->session()->put('checkout.shipping.apartment', $request->apartment);
      $request->session()->put('checkout.shipping.address', $request->address);
      $request->session()->put('checkout.shipping.city', $request->city);
      $request->session()->put('checkout.shipping.postal', $request->postal);
      $request->session()->put('checkout.shipping.country', $request->country);
      $request->session()->put('checkout.shipping.state', $request->state);
      $request->session()->put('checkout.shipping.contact', $request->contact);

      return redirect()->back();
    }

    public function paypal(Request $request)
    {
    	$payer = PayPal::Payer();
    	$payer->setPaymentMethod('paypal');

      $detail = PayPal::Details();
      $detail->setSubtotal(session('cart.total'));
      $detail->setShipping(session('cart.shipping.cost'));

    	$amount = PayPal::Amount();
      $amount->setCurrency(session('currency'));

      $total = session('cart.total') + session('cart.shipping.cost');
      // discount
      if(session()->has('checkout.voucher.value')) {
        $detail->setShippingDiscount(-session('checkout.voucher.value'));
        $total -= session('checkout.voucher.value');
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
          $item->setPrice($cartItem['price']);
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
          echo '<pre>';print_r(json_decode($pce->getData()));exit;
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
                  'name' => session('checkout.shipping.firstName').' '.session('checkout.shipping.lastName'),
                  'email' => session('checkout.shipping.email'),
                  'phone' => session('checkout.shipping.contact'),
                  'address_line_1' => session('checkout.shipping.address'),
                  'address_line_2' => session('checkout.shipping.appartment'),
                  'city' => session('checkout.shipping.city'),
                  'postcode' => session('checkout.shipping.postal'),
                  'state' => session('checkout.shipping.state'),
                  'country' => session('checkout.shipping.country'),
                  'shipping_cost' => session('cart.shipping.cost'),
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
                          'image' => $cartItem['image'],
                          'images' =>$cartItem['images'],
                          'thumb' => $cartItem['thumb'],
                          'back' => $cartItem['back'],
                          'type_id' => $type_id,
                          'description' => $cartItem['description'],
                          'price' => $cartItem['price'],
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

              // remove session cart
              session()->forget("cart");
              // send mail
              // $order->notify(new OrderSuccess($order));

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
      $presentation->setLogoImage("https://fomo.watch/images/demo/paypal-logo.svg")->setBrandName("FOMO"); //NB: Paypal recommended to use https for the logo's address and the size set to 190x60.
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

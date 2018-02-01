@component('mail::message')
# Thank you for your purchase!

Hi {{$order->name}}, we're getting your customize watch ready to be shipped. We will notify you when it has been sent.


Order {{$order->orderCode()}} summary:

@component('mail::table')
|Product                     |Quantity  |Price                 |
|:---------------------------|---------:|----------------------:|
@foreach($order->items as $item)
|**{{$item->product->name}}**|{{$item->quantity}} pcs|${{$item->product->price * $order->currency_rate}}      |
@endforeach
@endcomponent

@if($order->shipping_cost != 0)
**Shipping Charge: ${{number_format($order->shipping_cost * $order->currency_rate, 2)}}**
@endif

@if($order->discount)
**Discount: $ {{number_format($order->discount->amount* $order->currency_rate, 2)}}**
@endif

#Total: ${{number_format($order->amount())}}
@endcomponent

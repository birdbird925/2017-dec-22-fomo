@component('mail::message')
# Thank you for your purchase!

Hi {{$order->name}}, we're getting your customize watch ready to be shipped. We will notify you when it has been sent.


Order {{$order->orderCode()}} summary:

@component('mail::table')
|Product                            |Price                           |
|:----------------------------------|-------------------------------:|
@foreach($order->items as $item)
|**{{$item->product->name}}**<br>{{$item->product->description}}|${{$item->product->price}}      |
@endforeach
@endcomponent

@if($order->shipping_cost != 0)
**Shipping Charge: ${{number_format($order->shipping_cost, 2)}}**
@endif

#Total: ${{number_format($order->shipping_cost+$order->subTotal(), 2)}}
@endcomponent

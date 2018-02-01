<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Notifications\OrderCancel;
use App\Notifications\OrderFulfil;
use App\Notifications\ShipmentUpdate;
use App\Notifications\ShipmentCancel;
use App\Order;
use App\OrderShipment;
use App\ShipmentItem;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','auth.admin']);
    }

    public function test()
    {
        $shipment = OrderShipment::find(3);
        if(!$shipment) abort('404');

        $order = $shipment->order;
        if($order->order_status) {
            foreach($order->items as $item) {
                $item->fulfill = 0;
                $item->save();
            }
            $shipment->delete();

            // send mail
            $order->notify(new ShipmentCancel($order));
        }
    }

    public function index()
    {
        $orders = Order::orderBy('created_at', 'desc')->get();
        return view('admin.order.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::find($id);
        if(!$order) abort('404');
        $order->unreadNotifications->markAsRead();
        return view('admin.order.show', compact('order'));
    }

    public function cancel($id)
    {
        $order = Order::find($id);
        if(!$order) abort('404');

        if($order->order_status) {
            $order->order_status = 0;
            $order->save();

            // send mail
            // refund
            $order->notify(new OrderCancel($order));
        }

        return redirect('/admin/order/'.$id);
    }

    public function fulfill($id, Request $request)
    {
        $this->validate($request, [
            'tracking_code' => 'required',
            'shipment_carrier' => 'required',
            'tracking_url' => 'required|url'
        ]);

        $order = Order::find($id);
        if(!$order) abort('404');
        if($order->order_status) {
            $shipment = OrderShipment::create([
                'shipping_carrier' => $request->shipment_carrier,
                'tracking_number' => $request->tracking_code,
                'tracking_url' => $request->tracking_url,
                'order_id' => $id,
            ]);

            foreach($order->items as $item) {
                // update fulfill
                $item->fulfill = $item->quantity;
                $item->save();
                // add shipment item
                $shipmentItem = ShipmentItem::create([
                    'shipment_id' => $shipment->id,
                    'item_id' => $item->id,
                    'quantity' => $item->fulfill
                ]);
            }

            // send mail
            $order->notify(new OrderFulfil($shipment));
        }

        return redirect('/admin/order/'.$id);
    }

    public function updateShipment($id, Request $request)
    {
        $this->validate($request, [
            'tracking_code' => 'required',
            'shipment_carrier' => 'required',
            'tracking_url' => 'required|url'
        ]);

        $shipment = OrderShipment::find($id);
        if(!$shipment) abort('404');
        if($shipment->order->order_status) {
            $shipment->shipping_carrier = $request->shipment_carrier;
            $shipment->tracking_number = $request->tracking_code;
            $shipment->tracking_url = $request->tracking_url;
            $shipment->save();

            // send mail
            $shipment->order->notify(new ShipmentUpdate($shipment));
        }

        return redirect('/admin/order/'.$shipment->order->id);
    }

    public function cancelShipment($id)
    {
        $shipment = OrderShipment::find($id);
        if(!$shipment) abort('404');

        $order = $shipment->order;
        if($order->order_status) {
            foreach($order->items as $item) {
                $item->fulfill = 0;
                $item->save();
            }
            $shipment->delete();

            // send mail
            $order->notify(new ShipmentCancel($order));
        }

        return redirect('/admin/order/'.$order->id);
    }

}

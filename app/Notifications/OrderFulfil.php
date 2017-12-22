<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OrderFulfil extends Notification
{
    use Queueable;

    protected $order;

    public function __construct($shipment)
    {
        $this->order = $shipment->order;
        $this->shipment = $shipment;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('A shipment from order '.$this->order->orderCode().' is on the way')
                    ->greeting('Your order is on the way')
                    ->line('Your order is on the way to you. Track your shipment to see the delivery status.')
                    ->line('Shipping carrier: '.$this->shipment->shipping_carrier)
                    ->line('Tracking number: '.$this->shipment->tracking_number)
                    ->action('Track Shipment', url($this->shipment->tracking_url));
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

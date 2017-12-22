<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ShipmentUpdate extends Notification
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
                    ->subject('Shipping update for order '.$this->order->orderCode())
                    ->greeting('Your shipping status has been updated')
                    ->line('Your order have been updated with new shipping information.')
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

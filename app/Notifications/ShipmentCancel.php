<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ShipmentCancel extends Notification
{
    use Queueable;

    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Shipment of order '.$this->order->orderCode().' has  been cancel')
                    ->greeting('Shipment of order '.$this->order->orderCode().' has been cancel')
                    ->line('We are sorry for bring you any inconvenience, we will update you about shipment info soon.');
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

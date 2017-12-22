<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OrderCancel extends Notification
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
                    ->subject('Order '.$this->order->orderCode().' has been canceled')
                    ->greeting('Your order has been canceled')
                    ->line('Sorry, your order '.$this->order->orderCode().' was canceled and your payment has been voided.');
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

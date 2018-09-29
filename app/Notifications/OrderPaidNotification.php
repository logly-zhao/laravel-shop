<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Order;

class OrderPaidNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    // 我们只需要通过邮件通知，因此这里只需要一个 mail 即可
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $goods = '';
        foreach($this->order->items as $item) {
            $goods .= ($item->product->title."*".$item->amount."件，");

        }
        $title = $this->order->address['address'].'，'.$this->order->address['contact_name'].
            '，'.$this->order->address['contact_phone'].$goods;

        return (new MailMessage)
                    ->cc('18806130282@139.com')
                    ->subject($title)  // 邮件标题
                    ->greeting('您好：') // 欢迎词
                    ->line($this->order->created_at->format('m-d H:i').' 创建的订单已经支付成功。') // 邮件内容
                    ->action('查看订单', route('orders.show', [$this->order->id])) // 邮件中的按钮及对应链接
                    ->success(); // 按钮的色调
    }
}

<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class NewOrderCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public Order $order;

    public function __construct(Order $order)
    {
        // Perbarui: Muat semua relasi yang dibutuhkan oleh kedua dashboard
        $this->order = $order->load([
            'room.roomType', 
            'orderItems.menuItem',
            'kitchenStaff',
            'deliveryStaff'
        ]);
    }

    public function broadcastOn(): array
    {
        // Perbarui: Kirim event ke channel dapur dan pengantaran
        return [
            new PrivateChannel('kitchen-orders'),
            new PrivateChannel('delivery-dashboard'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'new-order';
    }
}
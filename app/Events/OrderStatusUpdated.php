<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class OrderStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public Order $order;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order)
    {
        // Muat semua relasi yang dibutuhkan oleh frontend
        $this->order = $order->load([
            'room.roomType',
            'orderItems.menuItem',
            'kitchenStaff',
            'deliveryStaff'
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Kirim pembaruan ke channel dapur dan pengantaran
        return [
            new PrivateChannel('kitchen-orders'),
            new PrivateChannel('delivery-dashboard'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        // Nama event yang jelas untuk didengarkan di frontend
        return 'order-status-updated';
    }
}
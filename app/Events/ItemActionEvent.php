<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ItemActionEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $item;    // The item that was affected
    public $action;  // Action type: restock, distribute, return, service, complete

    /**
     * Create a new event instance.
     *
     * @param  mixed  $item
     * @param  string  $action
     */
    public function __construct($item, string $action)
    {
        $this->item = $item;
        $this->action = $action;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn(): Channel|array
    {
        // Use a public channel called 'inventory' so all clients can listen
        return new Channel('inventory');
    }

    /**
     * Data to broadcast with the event
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'item' => $this->item,
            'action' => $this->action,
        ];
    }
}

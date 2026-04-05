<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ItemActionEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $item;
    public $action;

    public function __construct($item, string $action)
    {
        $this->item = $item;
        $this->action = $action;
    }

    public function broadcastOn(): Channel|array
    {
        return new Channel('inventory');
    }

    // ✅ ADD THIS
    public function broadcastAs(): string
    {
        return 'item.action';
    }

    public function broadcastWith(): array
    {
        return [
            'item' => $this->item,
            'action' => $this->action,
        ];
    }
}
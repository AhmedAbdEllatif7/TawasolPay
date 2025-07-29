<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessagesReadEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conversationId;
    public $messageId;

    public function __construct($conversationId, $messageId = null)
    {
        $this->conversationId = $conversationId;
        $this->messageId = $messageId;
    }

    public function broadcastOn()
    {
        return new Channel('message-read-channel');
    }

    public function broadcastAs()
    {
        return 'message-read';
    }
}
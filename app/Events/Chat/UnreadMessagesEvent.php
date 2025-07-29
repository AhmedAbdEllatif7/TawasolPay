<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class UnreadMessagesEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conversationId;
    public $lastMessage;
    public $unreadCount;
    public $messageTime;

    public function __construct($conversationId, $lastMessage, $unreadCount, $messageTime)
    {
        $this->conversationId = $conversationId;
        $this->lastMessage = $lastMessage;
        $this->unreadCount = $unreadCount;
        $this->messageTime = $messageTime;
    }

    public function broadcastOn()
    {
        return new Channel('unread-messages-channel');
    }

    public function broadcastAs()
    {
        return 'unread-updated';
    }
}

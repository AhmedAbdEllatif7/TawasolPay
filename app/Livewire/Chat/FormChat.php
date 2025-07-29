<?php

namespace App\Livewire\Chat;

use App\Events\Chat\ShowMessagesEvent;
use App\Events\Chat\MessagesReadEvent;
use App\Events\Chat\UnreadMessagesEvent;
use App\Models\Conversation;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;

class FormChat extends Component
{
    public $conversationId;
    public $message;
    public $isReceiverConversationOpened = false;
    public $files = [];
    protected $rules = [
        'message' => 'required|string|min:1|max:10000',
        'conversationId' => 'required|exists:conversations,id',
    ];

    protected $listeners = ['messagesRead' => 'markMessagesAsRead'];


    public function sendMessage()
    {
        $this->validate();

        $conversation = $this->getConversation();

        $message = $this->storeMessage($conversation);

        $this->broadcastMessage($message, $conversation);

        $this->reset('message');
    }


    protected function storeMessage($conversation)
    {
        return Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'message' => $this->message,
            'is_read' => false,
        ]);
    }



    protected function broadcastMessage($message, $conversation)
    {
        broadcast(new ShowMessagesEvent($message))->toOthers();

        if (!$this->isReceiverConversationOpened) {
            broadcast(new UnreadMessagesEvent(
                $conversation->id,
                $message->message,
                $conversation->unreadMessagesCountOfSender(),
                $message->created_at->diffForHumans()
            ));
        }
    }


    public function markMessagesAsRead()
    {
        $this->isReceiverConversationOpened = true;

        $conversation = $this->getConversation();
        
        $lastMessage = $conversation->lastMessage;

        if ($lastMessage && !$lastMessage->is_read && $lastMessage->sender_id !== Auth::id())
        {
            $lastMessage->update(['is_read' => true]);
            broadcast(new MessagesReadEvent($conversation->id, $lastMessage->id));
        }
        
    }


    protected function getConversation()
    {
        return Conversation::findOrFail($this->conversationId);
    }

    public function render()
    {
        return view('livewire.chat.form-chat');
    }
}
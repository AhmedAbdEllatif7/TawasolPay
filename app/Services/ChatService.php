<?php

namespace App\Services;

use App\Events\Chat\MessagesReadEvent;
use Illuminate\Support\Facades\Auth;
use App\Models\Conversation;


class ChatService 
{

    public function getUserConversations($user)
    {
        return Conversation::where('user_one_id', $user->id)
            ->orWhere('user_two_id', $user->id)
            ->with(['userOne', 'userTwo', 'lastMessage'])
            ->withMax('messages as latest_message_created_at', 'created_at')
            ->orderByDesc('latest_message_created_at')
            ->paginate(10);
    }


    public function getOrCreateConversationWith($otherUser)
    {
        $authUser = Auth::user();

        return Conversation::firstOrCreate([
            'user_one_id' => min($authUser->id, $otherUser->id),
            'user_two_id' => max($authUser->id, $otherUser->id),
        ]);
    }

    public function markMessagesAsRead($conversation)
    {
        $authUser = Auth::user();

        $conversation->messages()
            ->where('sender_id', '!=', $authUser->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
            broadcast(new MessagesReadEvent($conversation->id));
    }

    public function getMessages($conversation)
    {
        return $conversation->messages()->with('sender')->oldest()->get();
    }



}
<?php

namespace App\Models;

use App\Events\Chat\MessageReadEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Conversation extends Model
{
    protected $fillable = [
        'user_one_id',
        'user_two_id',
    ];

    // علاقة مع المستخدم الأول
    public function userOne(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_one_id');
    }

    // علاقة مع المستخدم الثاني
    public function userTwo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_two_id');
    }


    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function otherUser($currentUserId)
    {
        return $this->user_one_id == $currentUserId
            ? $this->userTwo
            : $this->userOne;
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function unreadMessagesCountForReciever()
    {
        return $this->getUnreadMessagesCountFor(Auth::id(), '!=');
    }

    public function unreadMessagesCountOfSender()
    {
        return $this->getUnreadMessagesCountFor(Auth::id(), '=');
    }

    private function getUnreadMessagesCountFor($userId, $operator)
    {
        return $this->messages()
            ->where('is_read', false)
            ->where('sender_id', $operator, $userId)
            ->count();
    }



    
}

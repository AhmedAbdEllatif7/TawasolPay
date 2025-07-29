<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class Message extends Model implements HasMedia
{

    use InteractsWithMedia;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'message',
        'is_read',
    ];

    // علاقة مع المحادثة
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

    // علاقة مع المرسل (المستخدم)
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }






}

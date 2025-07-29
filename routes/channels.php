<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
// في ملف routes/channels.php

Broadcast::channel('unread-messages.{userId}', function ($user, $userId) {
    // هذا يضمن أن المستخدم الحالي هو فقط من يمكنه الاستماع إلى قناته الخاصة
    return (int) $user->id === (int) $userId;
});

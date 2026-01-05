<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{userA}.{userB}', function ($user, $userA, $userB) {
    return in_array($user->id, [(int)$userA, (int)$userB]);
});

Broadcast::channel('notify.{userId}', function ($user, $userId) {
    return $user->id == $userId;
});

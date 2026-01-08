<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{userA}.{userB}', function ($user, $userA, $userB) {
    return in_array($user->id, [(int)$userA, (int)$userB]); // Chỉ 2 người có id userA hoặc userB mới được join vào channel
});

Broadcast::channel('notify.{userId}', function ($user, $userId) {
    return $user->id == $userId; // Mỗi user chỉ nghe notify của chính mình
});

Broadcast::channel('online', function ($user) {
    return ['id'=>$user->id,'name'=>$user->name];
});

<?php

namespace App\Contracts\Repository\Message;

use App\Models\Message;
use Illuminate\Support\Collection;

interface IMessageRepository
{
    public function save(Message $message): Message;

    public function getChatHistory(int $userId1, int $userId2): Collection;

    public function markAsRead(int $fromUserId, int $toUserId): int;

    public function getUnreadTotal(int $userId): int;
}

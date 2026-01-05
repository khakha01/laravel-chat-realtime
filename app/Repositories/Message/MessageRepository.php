<?php

namespace App\Repositories\Message;

use App\Contracts\Repository\Message\IMessageRepository;
use App\Models\Message;
use Illuminate\Support\Collection;

class MessageRepository implements IMessageRepository
{
    public function save(Message $message): Message
    {
        $message->save();
        return $message;
    }

    public function getChatHistory(int $userId1, int $userId2): Collection
    {
        return Message::where(function ($query) use ($userId1, $userId2) {
            $query->where('from_user_id', $userId1)->where('to_user_id', $userId2);})
            ->orWhere(function ($query) use ($userId1, $userId2) {
                $query->where('from_user_id', $userId2)
                    ->where('to_user_id', $userId1);
            })
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function markAsRead(int $fromUserId, int $toUserId): int
    {
        return Message::where('from_user_id', $fromUserId)
            ->where('to_user_id', $toUserId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    public function getUnreadTotal(int $userId): int
    {
        return Message::where('to_user_id', $userId)
            ->where('is_read', false)
            ->count();
    }
}

<?php

namespace App\Services;

use App\Contracts\Repository\Message\IMessageRepository;
use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ChatService
{

    public function __construct(protected IMessageRepository $messageRepository) {}

    public function sendMessage(User $fromUser, int $toUserId, string $content): Message
    {
        $message = Message::make($fromUser->id, $toUserId, $content);

        return DB::transaction(function () use ($message, $fromUser, $toUserId, $content) {
            $this->messageRepository->save($message);

            broadcast(new MessageSent($content, $fromUser, $fromUser->id, $toUserId))->toOthers();

            return $message;
        });
    }

    public function getHistory(int $authUserId, int $otherUserId): Collection
    {
        return $this->messageRepository->getChatHistory($authUserId, $otherUserId);
    }

    public function markMessagesAsRead(int $fromUserId, int $toUserId): int
    {
        return $this->messageRepository->markAsRead($fromUserId, $toUserId);
    }

    public function getUnreadTotal(int $userId): int
    {
        return $this->messageRepository->getUnreadTotal($userId);
    }
}

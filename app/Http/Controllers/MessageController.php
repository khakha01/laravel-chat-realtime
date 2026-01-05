<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Requests\Chat\StoreChatMessageRequest;
use App\Models\Message;
use App\Models\User;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController
{
    public function __construct(protected ChatService $chatService) {}

    public function index()
    {
        return view('user.chat');
    }

    public function chatMessage(StoreChatMessageRequest $request)
    {
        $fromUser = Auth::user();
        $toUserId = $request->get('to_user_id');
        $message = $request->get('message');
        $this->chatService->sendMessage($fromUser, $toUserId, $message);
        return response()->json(['status' => 'success']);
    }

    public function history($userId)
    {
        $authUserId = Auth::id();

        $messages = $this->chatService->getHistory($authUserId, $userId);

        return response()->json($messages);
    }

    public function getUnreadTotal()
    {
        $authUserId = Auth::id();
        $count = $this->chatService->getUnreadTotal($authUserId);

        return response()->json(['count' => $count]);
    }

    // đánh dấu là đã vào đoạn chat của 1 user
    public function markAsRead(User $user)
    {
        $authUserId = Auth::id();

        $this->chatService->markMessagesAsRead($user->id, $authUserId);

        return response()->json(['ok' => true]);
    }
}

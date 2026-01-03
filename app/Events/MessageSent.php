<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $message;
    public $user;

    public function __construct($message, $user = null)
    {
        $this->message = $message;
        $this->user = $user;
    }

    /**
     * broadcastOn() – Phát event trên kênh nào
     * Frontend phải listen đúng channel này mới nhận được event
     *
     * Channel // public
     * PrivateChannel // private (cần auth) bắt buộc xác thực
     * PresenceChannel // private + danh sách user online
     *
     * Nếu đổi channel → frontend phải đổi theo
     *
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat'), // Public channel
        ];
    }

    /**
     * broadcastAs() – Đặt tên cho event
     *
     * Đặt tên event gửi sang frontend
     * Nếu không có function này, Laravel sẽ dùng  App\Events\MessageSent
     *
     *
     * Khi có broadcastAs  .listen('.MessageSent', ...)
     * Khi KHÔNG có broadcastAs    .listen('App\\Events\\MessageSent', ...)
     *
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'MessageSent';
    }

    /**
     * Dữ liệu gửi sang frontend
     *
     * Xác định payload (data) của event
     * Frontend nhận được object này
     *
     *
     * @return void
     */
    public function broadcastWith()
    {
        return [
            'message' => [
                'content' => $this->message
            ],
            'user' => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ]
        ];
    }
}

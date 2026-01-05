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
    public $fromUserId;
    public $toUserId;

    public function __construct($message, $user, $fromUserId, $toUserId)
    {
        $this->message = $message;
        $this->user = $user;

        $this->fromUserId = $fromUserId;
        $this->toUserId   = $toUserId;
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
        $ids = collect([$this->fromUserId, $this->toUserId])->sort()->values();

        return [
            // channel chat 1–1
            new PrivateChannel("chat.{$ids[0]}.{$ids[1]}"),

            // channel notify cho người nhận
            new PrivateChannel("notify.{$this->toUserId}")
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

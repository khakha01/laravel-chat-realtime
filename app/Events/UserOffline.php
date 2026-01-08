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

class UserOffline implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public function __construct(public int $userId) {}

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
            new Channel('online-status'),
        ];
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
            'user_id' => $this->userId,
            'last_seen' => now()->toDateTimeString(),
        ];
    }
}

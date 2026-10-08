<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// ShouldBroadcastNow speaks for itself ig. trying out the reverb
class AchievementCreated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public int $userId;

    public array $achievement;

    public function __construct(int $userId, array $achievement)
    {
        $this->userId = $userId;
        $this->achievement = $achievement;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('users.' . $this->userId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'achievement.created';
    }
}

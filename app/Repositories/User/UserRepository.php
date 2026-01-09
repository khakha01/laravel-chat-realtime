<?php

namespace App\Repositories\User;

use App\Contracts\Repository\User\IUserRepository;
use App\Models\User;
use Illuminate\Support\Collection;

class UserRepository implements IUserRepository
{
    public function getAllExcept(User $excludeUser): Collection
    {
        return User::whereKeyNot($excludeUser)
            ->select('id', 'name', 'avatar', 'last_seen_at')
            ->get();
    }

    public function updateLastSeenAt(User $user): User
    {
        $user->update(['last_seen_at' => now()]);
        return $user;
    }

    public function getUserById(int $userId): ?User
    {
        return User::find($userId);
    }
}

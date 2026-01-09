<?php

namespace App\Contracts\Repository\User;

use App\Models\User;
use Illuminate\Support\Collection;

interface IUserRepository
{
    public function getAllExcept(User $excludeUser): Collection;

    public function updateLastSeenAt(User $user): User;

    public function getUserById(int $userId): ?User;
}

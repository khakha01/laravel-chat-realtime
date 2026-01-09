<?php

namespace App\Services;

use App\Contracts\Repository\User\IUserRepository;
use App\Events\UserStatus;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function __construct(protected IUserRepository $userRepository) {}

    public function getAllUsers(User $currentUser): Collection
    {
        return $this->userRepository->getAllExcept($currentUser);
    }

    public function markUserOffline(User $user): User
    {
        return DB::transaction(function () use ($user) {
            $updatedUser = $this->userRepository->updateLastSeenAt($user);

            broadcast(new UserStatus($user->id))->toOthers();

            return $updatedUser;
        });
    }
}

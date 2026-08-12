<?php

namespace App\Services;

use App\Models\User;

class FollowService
{
    public function follow(User $user, User $target): void
    {
        $user->following()->syncWithoutDetaching([
            $target->id,
        ]);
    }

    public function unfollow(User $user, User $target): void
    {
        $user->following()->detach($target->id);
    }
}

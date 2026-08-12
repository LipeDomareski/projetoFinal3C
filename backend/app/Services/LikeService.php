<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;

class LikeService
{
    public function like(User $user, Post $post): void
    {
        $user->likedPosts()->syncWithoutDetaching([$post->id]);
    }

    public function unlike(User $user, Post $post): void
    {
        $user->likedPosts()->detach($post->id);
    }
}

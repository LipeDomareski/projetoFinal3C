<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FeedService
{
    public function getFor(User $user): LengthAwarePaginator
    {
        $followingIds = $user->following()
            ->pluck('users.id')
            ->push($user->id);

        $posts = Post::query()
            ->with('user')
            ->withCount(['likedBy', 'comments'])
            ->whereIn('user_id', $followingIds)
            ->latest()
            ->paginate(10);

        $likedPostIds = $user->likedPosts()
            ->whereIn('posts.id', $posts->getCollection()->pluck('id'))
            ->pluck('posts.id')
            ->all();

        $posts->getCollection()->each(function (Post $post) use ($likedPostIds) {
            $post->setAttribute(
                'liked_by_me',
                in_array($post->id, $likedPostIds, true)
            );
        });

        return $posts;
    }
}

<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;

class CommentService
{
    public function create(
        User $user,
        Post $post,
        array $data
    ): Comment {
        return $post->comments()->create([
            'user_id' => $user->id,
            'content' => $data['content'],
        ]);
    }

    public function getByPost(Post $post)
    {
        return $post->comments()
            ->with('user')
            ->latest()
            ->get();
    }

    public function update(
        Comment $comment,
        array $data
    ): Comment {
        $comment->update($data);

        return $comment->load('user');
    }

    public function delete(Comment $comment): void
    {
        $comment->delete();
    }
}

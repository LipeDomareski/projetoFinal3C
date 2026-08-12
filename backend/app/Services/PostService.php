<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PostService
{
    public function create(User $user, array $data): Post
    {
        $data['image'] = $data['image']->store('posts', 'public');

        return $user->posts()
            ->create($data)
            ->load('user');
    }

    public function getAll(User $user): LengthAwarePaginator
    {
        $posts = Post::query()
            ->with('user')
            ->withCount(['likedBy', 'comments'])
            ->latest()
            ->paginate(10);

        $likedPostIds = $user->likedPosts()
            ->whereIn('posts.id', $posts->getCollection()->pluck('id'))
            ->pluck('posts.id')
            ->all();

        $posts->getCollection()->each(function (Post $post) use ($likedPostIds) {
            $post->setAttribute('liked_by_me', in_array($post->id, $likedPostIds, true));
        });

        return $posts;
    }

    public function find(int $id, ?User $user = null): Post
    {
        $post = Post::query()
            ->with('user')
            ->withCount(['likedBy', 'comments'])
            ->findOrFail($id);

        if ($user) {
            $post->setAttribute(
                'liked_by_me',
                $user->likedPosts()->where('posts.id', $post->id)->exists()
            );
        }

        return $post;
    }

    public function update(Post $post, array $data): Post
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }

            $data['image'] = $data['image']->store('posts', 'public');
        }

        $post->update($data);

        return $post->fresh()->load('user');
    }

    public function delete(Post $post): void
    {
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();
    }
}

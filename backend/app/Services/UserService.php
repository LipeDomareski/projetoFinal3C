<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    public function search(User $authenticatedUser, string $search = ''): Collection
    {
        return User::query()
            ->select(['id', 'name', 'username', 'bio', 'profile_photo'])
            ->where('id', '!=', $authenticatedUser->id)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('username', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('username')
            ->get();
    }

    public function suggestions(User $authenticatedUser, int $limit = 5): Collection
    {
        $followingIds = $authenticatedUser->following()
            ->pluck('users.id');

        return User::query()
            ->select(['id', 'name', 'username', 'bio', 'profile_photo'])
            ->where('id', '!=', $authenticatedUser->id)
            ->when(
                $followingIds->isNotEmpty(),
                fn ($query) => $query->whereNotIn('id', $followingIds)
            )
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    public function profile(User $authenticatedUser, int $userId): User
    {
        $user = User::query()
            ->select([
                'id',
                'name',
                'username',
                'bio',
                'profile_photo',
                'created_at',
                'updated_at',
            ])
            ->withCount(['posts', 'followers', 'following'])
            ->with([
                'posts' => fn ($query) => $query->latest(),
            ])
            ->findOrFail($userId);

        $isMe = $authenticatedUser->id === $user->id;

        $user->setAttribute('is_me', $isMe);
        $user->setAttribute(
            'is_following',
            !$isMe && $authenticatedUser
                ->following()
                ->where('users.id', $user->id)
                ->exists()
        );

        return $user;
    }
}

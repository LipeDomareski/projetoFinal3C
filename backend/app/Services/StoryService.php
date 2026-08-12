<?php

namespace App\Services;

use App\Models\Story;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class StoryService
{
    public function create(User $user, array $data): Story
    {
        $data['image'] = $data['image']->store('stories', 'public');
        $data['expires_at'] = now()->addHours(24);

        return $user->stories()
            ->create($data)
            ->load('user');
    }

    public function getActive()
    {
        return Story::with('user')
            ->where('expires_at', '>', now())
            ->latest()
            ->get();
    }

    public function find(int $id): Story
    {
        return Story::with('user')
            ->where('expires_at', '>', now())
            ->findOrFail($id);
    }

    public function delete(Story $story): void
    {
        if ($story->image) {
            Storage::disk('public')->delete($story->image);
        }

        $story->delete();
    }
}

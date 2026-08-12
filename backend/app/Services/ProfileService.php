<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
    public function update(User $user, array $data): User
    {
        if (
            isset($data['profile_photo']) &&
            $data['profile_photo'] instanceof UploadedFile
        ) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $data['profile_photo'] = $data['profile_photo']
                ->store('profiles', 'public');
        }

        $user->update($data);

        return $user->fresh();
    }
}

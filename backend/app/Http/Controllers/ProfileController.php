<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->loadCount(['posts', 'followers', 'following']);

        $user->load([
            'posts' => fn ($query) => $query->latest(),
        ]);

        $user->setAttribute('is_me', true);
        $user->setAttribute('is_following', false);

        return response()->json([
            'user' => $user,
        ]);
    }

    public function update(
        UpdateProfileRequest $request,
        ProfileService $profileService
    ): JsonResponse {
        $user = $profileService->update(
            $request->user(),
            $request->validated()
        );

        $user->loadCount(['posts', 'followers', 'following']);

        return response()->json([
            'message' => 'Perfil atualizado com sucesso!',
            'user' => $user,
        ]);
    }
}

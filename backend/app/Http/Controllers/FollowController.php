<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\FollowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowController extends Controller
{
    public function store(
        Request $request,
        int $userId,
        FollowService $followService
    ): JsonResponse {
        $target = User::findOrFail($userId);

        if ($request->user()->id === $target->id) {
            return response()->json([
                'message' => 'Você não pode seguir a si mesmo.',
            ], 422);
        }

        $followService->follow(
            $request->user(),
            $target
        );

        return response()->json([
            'message' => 'Usuário seguido com sucesso!',
        ]);
    }

    public function destroy(
        Request $request,
        int $userId,
        FollowService $followService
    ): JsonResponse {
        $target = User::findOrFail($userId);

        $followService->unfollow(
            $request->user(),
            $target
        );

        return response()->json([
            'message' => 'Usuário deixou de ser seguido.',
        ]);
    }

    public function followers(int $userId): JsonResponse
    {
        $user = User::findOrFail($userId);

        return response()->json([
            'followers' => $user->followers()->get(),
        ]);
    }

    public function following(int $userId): JsonResponse
    {
        $user = User::findOrFail($userId);

        return response()->json([
            'following' => $user->following()->get(),
        ]);
    }
}

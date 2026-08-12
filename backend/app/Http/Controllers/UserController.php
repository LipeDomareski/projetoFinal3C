<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request, UserService $userService): JsonResponse
    {
        $search = trim((string) $request->query('search', ''));

        $users = $userService->search(
            $request->user(),
            $search
        );

        return response()->json([
            'users' => $users,
        ]);
    }

    public function suggestions(Request $request, UserService $userService): JsonResponse
    {
        $limit = min(max((int) $request->query('limit', 5), 1), 20);

        return response()->json([
            'users' => $userService->suggestions($request->user(), $limit),
        ]);
    }

    public function show(
        Request $request,
        int $userId,
        UserService $userService
    ): JsonResponse {
        $user = $userService->profile(
            $request->user(),
            $userId
        );

        return response()->json([
            'user' => $user,
        ]);
    }
}

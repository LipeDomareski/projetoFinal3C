<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\LikeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function store(
        Request $request,
        int $postId,
        LikeService $likeService
    ): JsonResponse {
        $post = Post::findOrFail($postId);

        $likeService->like(
            $request->user(),
            $post
        );

        return response()->json([
            'message' => 'Post curtido com sucesso!',
        ]);
    }

    public function destroy(
        Request $request,
        int $postId,
        LikeService $likeService
    ): JsonResponse {
        $post = Post::findOrFail($postId);

        $likeService->unlike(
            $request->user(),
            $post
        );

        return response()->json([
            'message' => 'Curtida removida com sucesso!',
        ]);
    }
}

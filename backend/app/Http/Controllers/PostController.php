<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function store(CreatePostRequest $request, PostService $postService): JsonResponse
    {
        $post = $postService->create(
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'message' => 'Post criado com sucesso!',
            'post' => $post,
        ], 201);
    }

    public function index(Request $request, PostService $postService): JsonResponse
    {
        $posts = $postService->getAll($request->user());

        return response()->json([
            'posts' => $posts,
        ]);
    }

    public function show(Request $request, int $id, PostService $postService): JsonResponse
    {
        $post = $postService->find($id, $request->user());
        $post->setAttribute('is_owner', $post->user_id === $request->user()->id);

        return response()->json([
            'post' => $post,
        ]);
    }

    public function update(
        UpdatePostRequest $request,
        int $id,
        PostService $postService
    ): JsonResponse {
        $post = $postService->find($id, $request->user());

        if ($post->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Você não pode editar este post.',
            ], 403);
        }

        $post = $postService->update($post, $request->validated());

        return response()->json([
            'message' => 'Post atualizado com sucesso!',
            'post' => $post,
        ]);
    }

    public function destroy(Request $request, int $id, PostService $postService): JsonResponse
    {
        $post = $postService->find($id, $request->user());

        if ($post->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Você não pode excluir este post.',
            ], 403);
        }

        $postService->delete($post);

        return response()->json([
            'message' => 'Post excluído com sucesso!',
        ]);
    }
}

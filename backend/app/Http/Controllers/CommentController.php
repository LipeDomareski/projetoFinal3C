<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCommentRequest;
use App\Models\Post;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UpdateCommentRequest;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(
        CreateCommentRequest $request,
        int $postId,
        CommentService $commentService
    ): JsonResponse {
        $post = Post::findOrFail($postId);

        $comment = $commentService->create(
            $request->user(),
            $post,
            $request->validated()
        );

        $comment->load('user');

        return response()->json([
            'message' => 'Comentário criado com sucesso!',
            'comment' => $comment,
        ], 201);
    }

    public function index(
        int $postId,
        CommentService $commentService
    ): JsonResponse {
        $post = Post::findOrFail($postId);

        $comments = $commentService->getByPost($post);

        return response()->json([
            'comments' => $comments,
        ]);
    }

    public function update(
        UpdateCommentRequest $request,
        int $id,
        CommentService $commentService
    ): JsonResponse {
        $comment = Comment::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $comment = $commentService->update(
            $comment,
            $request->validated()
        );

        return response()->json([
            'message' => 'Comentário atualizado com sucesso!',
            'comment' => $comment,
        ]);
    }

    public function destroy(
        Request $request,
        int $id,
        CommentService $commentService
    ): JsonResponse {
        $comment = Comment::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $commentService->delete($comment);

        return response()->json([
            'message' => 'Comentário excluído com sucesso!',
        ]);
    }
}

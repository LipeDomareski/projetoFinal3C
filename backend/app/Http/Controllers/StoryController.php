<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateStoryRequest;
use App\Services\StoryService;
use Illuminate\Http\JsonResponse;

class StoryController extends Controller
{
    public function store(
        CreateStoryRequest $request,
        StoryService $storyService
    ): JsonResponse {
        $story = $storyService->create(
            $request->user(),
            $request->validated()
        );

        return response()->json([
            'message' => 'Story criado com sucesso!',
            'story' => $story,
        ], 201);
    }

    public function index(StoryService $storyService): JsonResponse
    {
        $stories = $storyService->getActive();

        return response()->json([
            'stories' => $stories,
        ]);
    }

    public function show(
        int $id,
        StoryService $storyService
    ): JsonResponse {
        $story = $storyService->find($id);

        return response()->json([
            'story' => $story,
        ]);
    }

    public function destroy(
        int $id,
        StoryService $storyService
    ): JsonResponse {
        $story = $storyService->find($id);

        if ($story->user_id !== request()->user()->id) {
            return response()->json([
                'message' => 'Você não pode excluir este Story.',
            ], 403);
        }

        $storyService->delete($story);

        return response()->json([
            'message' => 'Story excluído com sucesso!',
        ]);
    }
}

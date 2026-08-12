<?php

namespace App\Http\Controllers;

use App\Services\FeedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    public function index(Request $request, FeedService $feedService): JsonResponse
    {
        return response()->json([
            'posts' => $feedService->getFor($request->user()),
        ]);
    }
}

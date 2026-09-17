<?php

namespace App\Http\Controllers\Api\V1;

use App\Forum\ForumBoard;
use App\Forum\ForumWriter;
use App\Http\Requests\StartForumThreadRequest;
use App\Http\Resources\ForumThreadResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ForumThreadController extends ApiController
{
    public function index(Request $request, ForumBoard $board): AnonymousResourceCollection
    {
        return ForumThreadResource::collection($board->threadsOf($this->employment($request)->company));
    }

    public function store(StartForumThreadRequest $request, ForumWriter $writer): JsonResponse
    {
        $thread = $writer->startThread($this->player($request), $request->title(), $request->body());

        return (new ForumThreadResource($thread->load('authorPosition.person')))->response()->setStatusCode(201);
    }
}

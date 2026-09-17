<?php

namespace App\Http\Controllers\Api\V1;

use App\Forum\ForumBoard;
use App\Forum\ForumWriter;
use App\Http\Requests\WriteForumPostRequest;
use App\Http\Resources\ForumPostResource;
use App\Models\ForumPost;
use App\Models\ForumThread;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ForumPostController extends ApiController
{
    public function index(Request $request, ForumThread $forumThread, ForumBoard $board): AnonymousResourceCollection
    {
        return ForumPostResource::collection($board->postsOf($forumThread));
    }

    public function store(WriteForumPostRequest $request, ForumThread $forumThread, ForumWriter $writer): JsonResponse
    {
        $post = $writer->reply($this->player($request), $forumThread, $request->body());

        return (new ForumPostResource($post->load('authorPosition.person')))->response()->setStatusCode(201);
    }

    public function destroy(Request $request, ForumPost $forumPost): Response
    {
        $forumPost->delete();

        return response()->noContent();
    }
}

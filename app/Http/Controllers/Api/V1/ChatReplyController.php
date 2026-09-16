<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\ReplyToChatMessageRequest;
use App\Http\Resources\ChatMessageResource;
use App\Messenger\ChatReplier;
use App\Models\ChatMessage;
use Illuminate\Http\JsonResponse;

class ChatReplyController extends ApiController
{
    public function store(ReplyToChatMessageRequest $request, ChatMessage $chatMessage, ChatReplier $replier): JsonResponse
    {
        $sent = $replier->reply($this->player($request), $chatMessage, $request->replySlug());

        return (new ChatMessageResource($sent))->response()->setStatusCode(201);
    }
}

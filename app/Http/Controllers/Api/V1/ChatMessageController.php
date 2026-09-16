<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ChatMessageResource;
use App\Messenger\ChatInbox;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ChatMessageController extends ApiController
{
    public function index(Request $request, ChatInbox $inbox): AnonymousResourceCollection
    {
        return ChatMessageResource::collection($inbox->messagesOf($this->player($request)));
    }
}

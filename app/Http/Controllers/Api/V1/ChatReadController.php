<?php

namespace App\Http\Controllers\Api\V1;

use App\Messenger\ChatInbox;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ChatReadController extends ApiController
{
    public function store(Request $request, ChatInbox $inbox): Response
    {
        $inbox->markAllRead($this->player($request));

        return response()->noContent();
    }
}

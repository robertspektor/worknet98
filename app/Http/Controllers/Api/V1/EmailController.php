<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\MailboxRequest;
use App\Http\Requests\SendWorkEmailRequest;
use App\Http\Resources\EmailResource;
use App\Mailbox\MailboxQuery;
use App\Mailbox\WorkEmailSender;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmailController extends ApiController
{
    public function index(MailboxRequest $request, MailboxQuery $mailbox): AnonymousResourceCollection
    {
        return EmailResource::collection($mailbox->emailsOf($this->player($request), $request->scope()));
    }

    public function store(SendWorkEmailRequest $request, WorkEmailSender $sender): JsonResponse
    {
        $email = $sender->send($this->player($request), $request->outgoingEmail());

        return (new EmailResource($email))->response()->setStatusCode(201);
    }
}

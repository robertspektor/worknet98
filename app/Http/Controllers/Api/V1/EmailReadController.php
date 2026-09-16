<?php

namespace App\Http\Controllers\Api\V1;

use App\Mailbox\Mailbox;
use App\Models\Email;
use Illuminate\Http\Response;

class EmailReadController extends ApiController
{
    public function store(Email $email, Mailbox $mailbox): Response
    {
        $mailbox->markRead($email);

        return response()->noContent();
    }
}

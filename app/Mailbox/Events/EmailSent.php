<?php

namespace App\Mailbox\Events;

use App\Models\Email;
use Illuminate\Foundation\Events\Dispatchable;

class EmailSent
{
    use Dispatchable;

    public function __construct(public readonly Email $email) {}
}

<?php

namespace App\Mailbox;

enum EmailFolder: string
{
    case Inbox = 'inbox';
    case Sent = 'sent';
}

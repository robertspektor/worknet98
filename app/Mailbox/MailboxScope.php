<?php

namespace App\Mailbox;

enum MailboxScope: string
{
    case Private = 'private';
    case Work = 'work';
}

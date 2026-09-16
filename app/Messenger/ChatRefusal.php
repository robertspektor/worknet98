<?php

namespace App\Messenger;

use App\Game\Refusal;

enum ChatRefusal: string implements Refusal
{
    case AlreadyReplied = 'already_replied';
    case UnknownReply = 'unknown_reply';

    public function message(): string
    {
        return __('messenger.refusal.'.$this->value);
    }
}

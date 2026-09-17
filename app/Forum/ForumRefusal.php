<?php

namespace App\Forum;

use App\Game\Refusal;

enum ForumRefusal: string implements Refusal
{
    case TooFast = 'too_fast';

    public function message(): string
    {
        return __('forum.refusal.'.$this->value);
    }
}

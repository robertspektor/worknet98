<?php

namespace App\Game;

interface Refusal extends \BackedEnum
{
    public function message(): string;
}

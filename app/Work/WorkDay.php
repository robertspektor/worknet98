<?php

namespace App\Work;

class WorkDay
{
    public function today(): string
    {
        return now()->toDateString();
    }
}

<?php

namespace App\Cases;

readonly class ScriptedCase
{
    public function __construct(
        public int $shift,
        public CaseDefinition $definition,
    ) {}
}

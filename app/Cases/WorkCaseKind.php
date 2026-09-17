<?php

namespace App\Cases;

enum WorkCaseKind: string
{
    case Scripted = 'scripted';
    case Template = 'template';
}

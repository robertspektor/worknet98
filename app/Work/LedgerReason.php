<?php

namespace App\Work;

enum LedgerReason: string
{
    case Salary = 'salary';
    case Purchase = 'purchase';
    case Bonus = 'bonus';
    case LivingCosts = 'living_costs';
}

<?php

namespace App\Cases;

enum Metric: string
{
    case CustomerSatisfaction = 'customer_satisfaction';
    case Punctuality = 'punctuality';
    case Cost = 'cost';
}

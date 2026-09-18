<?php

namespace App\Milestones\Conditions;

use App\Work\Wallet;
use InvalidArgumentException;

class MilestoneConditionFactory
{
    public function __construct(private readonly Wallet $wallet) {}

    /**
     * @param  array<string, mixed>  $definition
     */
    public function make(array $definition): MilestoneCondition
    {
        return match ($definition['type']) {
            'balance' => new Balance($this->wallet, (int) $definition['amount']),
            'employments' => new EmploymentCount((int) $definition['count']),
            'shifts' => new ShiftCount((int) $definition['count']),
            'cases_resolved' => new ResolvedCaseCount((int) $definition['count']),
            'weekly_goals' => new WeeklyGoalCount((int) $definition['count']),
            'excellent_reviews' => new ExcellentReviewCount((int) $definition['count']),
            'promotions' => new PromotionCount((int) $definition['count']),
            'employee_awards' => new EmployeeAwardCount((int) $definition['count']),
            'orders_unpacked' => new UnpackedOrderCount((int) $definition['count']),
            'own_hardware_installed' => new OwnHardwareInstalled,
            default => throw new InvalidArgumentException("Unknown milestone condition [{$definition['type']}]."),
        };
    }
}

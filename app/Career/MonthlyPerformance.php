<?php

namespace App\Career;

use App\Cases\Metric;
use App\Cases\MetricBook;
use App\Models\Employment;
use App\Models\PerformanceReview;

readonly class MonthlyPerformance
{
    /**
     * @param  array<string, int>  $totals
     * @param  array<string, int>  $changes
     */
    public function __construct(
        public array $totals,
        public array $changes,
    ) {}

    public static function of(Employment $employment, MetricBook $metrics): self
    {
        $previous = PerformanceReview::query()->whereBelongsTo($employment)->latest('period')->first()->metric_totals ?? [];
        $totals = [];
        $changes = [];

        foreach (Metric::cases() as $metric) {
            $totals[$metric->value] = $metrics->valueOf($employment, $metric);
            $changes[$metric->value] = $totals[$metric->value] - ($previous[$metric->value] ?? 0);
        }

        return new self($totals, $changes);
    }

    public function score(): int
    {
        return array_sum($this->changes);
    }
}

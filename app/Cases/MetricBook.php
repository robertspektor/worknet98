<?php

namespace App\Cases;

use App\Models\Employment;
use App\Models\EmploymentMetric;

class MetricBook
{
    /**
     * @param  array<string, int>  $effects
     */
    public function apply(Employment $employment, array $effects): void
    {
        foreach ($effects as $metric => $delta) {
            $this->add($employment, Metric::from($metric), $delta);
        }
    }

    public function valueOf(Employment $employment, Metric $metric): int
    {
        return (int) EmploymentMetric::query()
            ->where('employment_id', $employment->id)
            ->where('metric', $metric)
            ->value('value');
    }

    private function add(Employment $employment, Metric $metric, int $delta): void
    {
        $record = EmploymentMetric::firstOrCreate(['employment_id' => $employment->id, 'metric' => $metric]);
        $record->increment('value', $delta);
    }
}

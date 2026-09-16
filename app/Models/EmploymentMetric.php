<?php

namespace App\Models;

use App\Cases\Metric;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $employment_id
 * @property Metric $metric
 * @property int $value
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 */
#[Fillable(['employment_id', 'metric', 'value'])]
class EmploymentMetric extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metric' => Metric::class,
        ];
    }
}

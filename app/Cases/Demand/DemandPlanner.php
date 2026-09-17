<?php

namespace App\Cases\Demand;

use App\Cases\Templates\CaseTemplate;
use App\Models\Branch;
use App\Models\Position;
use Carbon\CarbonImmutable;
use Random\Engine\Mt19937;
use Random\Randomizer;

class DemandPlanner
{
    private const MAX_CASES_PER_POSITION = 2;

    private const FIRST_MINUTE = 8 * 60;

    private const LAST_MINUTE = 16 * 60;

    /**
     * @param  list<CaseTemplate>  $templates
     * @return list<Demand>
     */
    public function plan(Branch $branch, array $templates, CarbonImmutable $day): array
    {
        if (! $day->isWeekday() || $templates === []) {
            return [];
        }

        $seed = "{$branch->company->slug}|{$branch->slug}|{$day->toDateString()}";
        $randomizer = new Randomizer(new Mt19937(crc32($seed)));
        $demands = [];

        foreach (collect($templates)->groupBy('responsibility') as $responsibility => $candidates) {
            $count = $this->caseCount($randomizer, $branch, (string) $responsibility);

            for ($number = 1; $number <= $count; $number++) {
                $demands[] = new Demand(
                    key: "{$seed}|{$responsibility}|{$number}",
                    opensAt: $day->addMinutes($randomizer->getInt(self::FIRST_MINUTE, self::LAST_MINUTE)),
                    template: $candidates[$randomizer->getInt(0, $candidates->count() - 1)],
                );
            }
        }

        return $demands;
    }

    private function caseCount(Randomizer $randomizer, Branch $branch, string $responsibility): int
    {
        $positions = Position::query()->whereBelongsTo($branch)->responsibleFor($responsibility)->count();

        return array_sum(array_map(
            fn (): int => $randomizer->getInt(0, self::MAX_CASES_PER_POSITION),
            range(1, max(1, $positions)),
        )) * ($positions > 0 ? 1 : 0);
    }
}

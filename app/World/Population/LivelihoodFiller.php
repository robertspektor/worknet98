<?php

namespace App\World\Population;

use App\Models\City;
use App\Models\Person;
use App\Models\Position;
use Random\Engine\Mt19937;
use Random\Randomizer;

class LivelihoodFiller
{
    private const WORK_DAYS_PER_MONTH = 21;

    public function __construct(private readonly Livelihoods $livelihoods) {}

    public function fillMissing(City $city): void
    {
        $pool = NamePool::forLocale($city->locale);

        Person::query()
            ->whereBelongsTo($city)
            ->whereNull('occupation')
            ->lazyById()
            ->each(function (Person $person) use ($pool): void {
                $livelihood = $this->livelihoods->draw(new Randomizer(new Mt19937(crc32("livelihood|{$person->slug}"))), $pool);

                $person->update([
                    'occupation' => $livelihood->occupation,
                    'profession' => $livelihood->profession,
                    'monthly_income' => $livelihood->monthlyIncome,
                ]);
            });
    }

    public function employAt(Position $position): void
    {
        $person = $position->person;

        $person->update([
            'occupation' => Occupation::Employed,
            'profession' => $position->title,
            'monthly_income' => $position->daily_salary === null
                ? $person->monthly_income
                : $position->daily_salary * self::WORK_DAYS_PER_MONTH,
        ]);
    }
}

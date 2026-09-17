<?php

namespace App\CivilRegistry;

use App\Game\GameClock;
use App\Models\Branch;
use App\Models\CivilApplication;
use App\Models\Person;
use App\Models\WorldEvent;
use App\World\CityCatalog;
use Random\Engine\Mt19937;
use Random\Randomizer;

class ApplicationFactory
{
    private const MISTAKE_PERCENT = 30;

    public function __construct(
        private readonly GameClock $clock,
        private readonly CityCatalog $cities,
        private readonly PartnerPicker $partners,
        private readonly NewAddressPicker $addresses,
        private readonly ClaimedAddress $claims,
    ) {}

    public function create(Branch $office, WorldEvent $event, ApplicationKind $kind): ?CivilApplication
    {
        $randomizer = new Randomizer(new Mt19937(crc32("application|{$event->key}")));
        $applicant = $event->person;
        $hasMistake = $randomizer->getInt(1, 100) <= self::MISTAKE_PERCENT;
        $attributes = [
            'branch_id' => $office->id,
            'kind' => $kind,
            'applicant_id' => $applicant->id,
            'moved_on' => $this->clock->fromReal($event->occurred_at)->toDateString(),
        ];

        if ($kind === ApplicationKind::Move) {
            return CivilApplication::create([...$attributes, ...$this->move($event, $hasMistake, $randomizer)]);
        }

        $partner = $this->partners->for($applicant, $randomizer);

        return $partner === null ? null : CivilApplication::create([...$attributes, ...$this->marriage($event, $partner, $hasMistake, $randomizer)]);
    }

    /**
     * @return array<string, mixed>
     */
    private function move(WorldEvent $event, bool $hasMistake, Randomizer $randomizer): array
    {
        $claimed = $this->claims->of($event->person->household, $hasMistake, $randomizer);
        $new = $this->addresses->pick($event->city, $this->districtsOf($event), $randomizer);

        return [
            'claimed_district' => $claimed['district'],
            'claimed_street' => $claimed['street'],
            'new_district' => $new['district'],
            'new_street' => $new['street'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function marriage(WorldEvent $event, Person $partner, bool $hasMistake, Randomizer $randomizer): array
    {
        $mistakeAtPartner = $hasMistake && $randomizer->getInt(0, 1) === 1;
        $claimed = $this->claims->of($event->person->household, $hasMistake && ! $mistakeAtPartner, $randomizer);
        $claimedPartner = $this->claims->of($partner->household, $mistakeAtPartner, $randomizer);

        return [
            'partner_id' => $partner->id,
            'claimed_district' => $claimed['district'],
            'claimed_street' => $claimed['street'],
            'claimed_partner_district' => $claimedPartner['district'],
            'claimed_partner_street' => $claimedPartner['street'],
        ];
    }

    /**
     * @return list<string>
     */
    private function districtsOf(WorldEvent $event): array
    {
        return collect($this->cities->cities())->firstWhere('slug', $event->city->slug)['districts'] ?? [];
    }
}

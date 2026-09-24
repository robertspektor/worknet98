<?php

namespace App\Landing;

use App\Citynet\CityOverviews;
use App\Game\GameClock;
use App\Models\City;
use App\Models\Shipment;
use Illuminate\Support\Facades\Cache;

class LandingFigures
{
    private const CACHE_MINUTES = 5;

    public function __construct(
        private CityOverviews $overviews,
        private GameClock $clock,
    ) {}

    public function forLocale(string $locale): ?CityFigures
    {
        /** @var array{city: string, residents: int, companies: int, open_positions: int, deliveries: int}|null $figures */
        $figures = Cache::remember(
            "landing.figures.{$locale}",
            now()->addMinutes(self::CACHE_MINUTES),
            fn (): ?array => $this->read($locale),
        );

        if ($figures === null) {
            return null;
        }

        return new CityFigures(
            city: $figures['city'],
            residents: $figures['residents'],
            companies: $figures['companies'],
            openPositions: $figures['open_positions'],
            deliveries: $figures['deliveries'],
        );
    }

    /**
     * @return array{city: string, residents: int, companies: int, open_positions: int, deliveries: int}|null
     */
    private function read(string $locale): ?array
    {
        $city = City::query()->where('locale', $locale)->first();

        if ($city === null) {
            return null;
        }

        $overview = $this->overviews->of($city);

        return [
            'city' => $city->name,
            'residents' => $overview->residents,
            'companies' => $overview->organizations,
            'open_positions' => $overview->openPositions,
            'deliveries' => $this->deliveriesThisGameMonth($city),
        ];
    }

    private function deliveriesThisGameMonth(City $city): int
    {
        return Shipment::query()
            ->whereRelation('branch', 'city_id', $city->id)
            ->where('delivered_at', '>=', $this->clock->toReal($this->clock->now()->startOfMonth()))
            ->count();
    }
}

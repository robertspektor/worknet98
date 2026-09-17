<?php

namespace App\Living;

use Illuminate\Support\Facades\File;

class LivingCostCatalog
{
    /**
     * @return list<LivingCost>
     */
    public function forLocale(string $locale): array
    {
        /** @var array<string, list<array{kind: string, sender_name: string, sender_address: string, amount: int}>> $costs */
        $costs = File::json(database_path('content/living_costs.json'), JSON_THROW_ON_ERROR);

        return array_map(fn (array $cost): LivingCost => new LivingCost(
            kind: $cost['kind'],
            senderName: $cost['sender_name'],
            senderAddress: $cost['sender_address'],
            amount: $cost['amount'],
        ), $costs[$locale] ?? []);
    }
}

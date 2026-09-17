<?php

namespace App\Career\Promotions;

use App\Models\Employment;
use App\Models\Position;
use Illuminate\Database\Eloquent\Collection;

class PromotionTargets
{
    /**
     * @return Collection<int, Position>
     */
    public function vacantFor(Employment $employment): Collection
    {
        return $employment->position->promotionTargets()
            ->whereNotNull('daily_salary')
            ->vacant()
            ->orderBy('positions.id')
            ->get();
    }
}

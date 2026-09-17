<?php

namespace App\Work;

use App\Models\Employment;
use App\Models\Shift;

class OpenShift
{
    public function of(Employment $employment): ?Shift
    {
        return Shift::query()
            ->where('employment_id', $employment->id)
            ->whereNull('clocked_out_at')
            ->first();
    }
}

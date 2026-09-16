<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Work\Wallet;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class PlayerResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'email' => $this->email,
            'locale' => $this->locale,
            'employer' => $this->employment?->company->name,
            'balance' => app(Wallet::class)->balanceOf($this->resource),
        ];
    }
}

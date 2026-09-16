<?php

namespace App\Game;

use Illuminate\Contracts\Debug\ShouldntReport;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class ActionRefused extends RuntimeException implements ShouldntReport
{
    public function __construct(public readonly Refusal $refusal)
    {
        parent::__construct($refusal->message());
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'refusal' => $this->refusal->value,
        ], 422);
    }
}

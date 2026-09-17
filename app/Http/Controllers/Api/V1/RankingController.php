<?php

namespace App\Http\Controllers\Api\V1;

use App\Career\Rankings;
use App\Http\Resources\EmployeeAwardResource;
use App\Http\Resources\RankingEntryResource;
use App\Models\EmployeeAward;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RankingController extends ApiController
{
    public function show(Request $request, Rankings $rankings): JsonResponse
    {
        $branch = $this->employment($request)->branch();
        $awards = EmployeeAward::query()
            ->whereBelongsTo($branch)
            ->with(['employment.position.person', 'branch'])
            ->latest('period')
            ->limit(3)
            ->get();

        return response()->json([
            'data' => [
                'branch' => RankingEntryResource::collection($rankings->ofBranch($branch))->resolve($request),
                'world' => RankingEntryResource::collection($rankings->ofWorld())->resolve($request),
                'awards' => EmployeeAwardResource::collection($awards)->resolve($request),
            ],
        ]);
    }
}

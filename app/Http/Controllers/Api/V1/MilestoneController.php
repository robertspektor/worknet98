<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\MilestoneResource;
use App\Milestones\MilestoneBoard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MilestoneController extends ApiController
{
    public function index(Request $request, MilestoneBoard $board): JsonResponse
    {
        return response()->json([
            'data' => MilestoneResource::collection($board->for($this->player($request)))->resolve($request),
        ]);
    }
}

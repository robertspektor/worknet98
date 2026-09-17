<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ShipmentResource;
use App\Logistics\ShipmentLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ShipmentController extends ApiController
{
    public function index(Request $request, ShipmentLog $log): AnonymousResourceCollection
    {
        return ShipmentResource::collection($log->recentOf($this->employment($request)->branch()));
    }
}

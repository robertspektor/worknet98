<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ParcelResource;
use App\Models\FloppyDiskOrder;
use App\Shop\ParcelUnpacker;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ParcelController extends ApiController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $parcels = FloppyDiskOrder::query()
            ->where('user_id', $this->player($request)->id)
            ->waitingOnDesk()
            ->with('floppyDisk')
            ->orderBy('delivered_at')
            ->orderBy('id')
            ->get();

        return ParcelResource::collection($parcels);
    }

    public function store(FloppyDiskOrder $floppyDiskOrder, ParcelUnpacker $unpacker): Response
    {
        $unpacker->unpack($floppyDiskOrder);

        return response()->noContent();
    }
}

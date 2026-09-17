<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ParcelResource;
use App\Models\Order;
use App\Shop\ParcelUnpacker;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ParcelController extends ApiController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $parcels = Order::query()
            ->where('user_id', $this->player($request)->id)
            ->waitingOnDesk()
            ->with('product')
            ->orderBy('delivered_at')
            ->orderBy('id')
            ->get();

        return ParcelResource::collection($parcels);
    }

    public function store(Order $order, ParcelUnpacker $unpacker): Response
    {
        $unpacker->unpack($order);

        return response()->noContent();
    }
}

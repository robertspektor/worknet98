<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ShopItemResource;
use App\Models\FloppyDisk;
use App\Shop\DiskOrderPlacer;
use App\Shop\DiskShop;
use App\Shop\ShopItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ShopController extends ApiController
{
    public function index(Request $request, DiskShop $shop): AnonymousResourceCollection
    {
        return ShopItemResource::collection($shop->catalogFor($this->player($request)));
    }

    public function store(Request $request, FloppyDisk $floppyDisk, DiskOrderPlacer $placer): JsonResponse
    {
        $order = $placer->place($this->player($request), $floppyDisk);

        return (new ShopItemResource(new ShopItem($floppyDisk, $order)))->response()->setStatusCode(201);
    }
}

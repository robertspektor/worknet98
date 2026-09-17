<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ShopItemResource;
use App\Models\FloppyDisk;
use App\Shop\Catalog;
use App\Shop\OrderPlacer;
use App\Shop\ShopItem;
use App\Shop\Storefront;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ShopController extends ApiController
{
    public function index(Request $request, Catalog $catalog): AnonymousResourceCollection
    {
        return ShopItemResource::collection($catalog->for($this->player($request), Storefront::DiskDepot));
    }

    public function store(Request $request, FloppyDisk $floppyDisk, OrderPlacer $placer): JsonResponse
    {
        $order = $placer->place($this->player($request), $floppyDisk);

        return (new ShopItemResource(new ShopItem($floppyDisk, $order)))->response()->setStatusCode(201);
    }
}

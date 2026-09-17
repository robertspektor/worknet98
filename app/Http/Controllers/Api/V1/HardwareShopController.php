<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\ShopItemResource;
use App\Models\HardwarePart;
use App\Shop\Catalog;
use App\Shop\OrderPlacer;
use App\Shop\ShopItem;
use App\Shop\Storefront;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HardwareShopController extends ApiController
{
    public function index(Request $request, Catalog $catalog): AnonymousResourceCollection
    {
        return ShopItemResource::collection($catalog->for($this->player($request), Storefront::ChipCity));
    }

    public function store(Request $request, HardwarePart $hardwarePart, OrderPlacer $placer): JsonResponse
    {
        $order = $placer->place($this->player($request), $hardwarePart);

        return (new ShopItemResource(new ShopItem($hardwarePart, $order)))->response()->setStatusCode(201);
    }
}

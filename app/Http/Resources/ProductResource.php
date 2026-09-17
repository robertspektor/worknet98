<?php

namespace App\Http\Resources;

use App\Models\FloppyDisk;
use App\Models\HardwarePart;
use App\Shop\Product;
use Illuminate\Http\Resources\Json\JsonResource;
use LogicException;

class ProductResource
{
    public static function of(Product $product): JsonResource
    {
        return match (true) {
            $product instanceof FloppyDisk => new FloppyDiskResource($product),
            $product instanceof HardwarePart => new HardwarePartResource($product),
            default => throw new LogicException('No resource for product '.$product::class),
        };
    }
}

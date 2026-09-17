<?php

namespace App\Http\Controllers\Api\V1;

use App\FloppyDisks\DiskBox;
use App\Http\Resources\PlayerFloppyDiskResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FloppyDiskController extends ApiController
{
    public function index(Request $request, DiskBox $diskBox): AnonymousResourceCollection
    {
        return PlayerFloppyDiskResource::collection($diskBox->disksFor($this->player($request)));
    }
}

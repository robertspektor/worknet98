<?php

namespace App\Http\Controllers\Api\V1;

use App\FloppyDisks\DiskBox;
use App\Http\Resources\FloppyDiskResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FloppyDiskController extends ApiController
{
    public function index(Request $request, DiskBox $diskBox): AnonymousResourceCollection
    {
        return FloppyDiskResource::collection($diskBox->disksFor($this->player($request)));
    }
}

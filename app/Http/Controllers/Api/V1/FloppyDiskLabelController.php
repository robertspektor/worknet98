<?php

namespace App\Http\Controllers\Api\V1;

use App\FloppyDisks\DiskLabeler;
use App\Http\Requests\LabelFloppyDiskRequest;
use App\Http\Resources\PlayerFloppyDiskResource;
use App\Models\PlayerFloppyDisk;

class FloppyDiskLabelController extends ApiController
{
    public function update(LabelFloppyDiskRequest $request, PlayerFloppyDisk $disk, DiskLabeler $labeler): PlayerFloppyDiskResource
    {
        $labeler->label($disk, $request->label());

        return new PlayerFloppyDiskResource($disk);
    }
}

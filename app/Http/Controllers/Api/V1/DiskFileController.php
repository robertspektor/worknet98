<?php

namespace App\Http\Controllers\Api\V1;

use App\FloppyDisks\DiskWriter;
use App\Http\Requests\SaveDiskFileRequest;
use App\Http\Resources\DiskFileResource;
use App\Models\DiskFile;
use App\Models\PlayerFloppyDisk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class DiskFileController extends ApiController
{
    public function index(PlayerFloppyDisk $disk): AnonymousResourceCollection
    {
        return DiskFileResource::collection($disk->files()->orderBy('name')->get());
    }

    public function store(SaveDiskFileRequest $request, PlayerFloppyDisk $disk, DiskWriter $writer): JsonResponse
    {
        $file = $writer->save($disk, $request->fileName(), $request->body());

        return (new DiskFileResource($file))->response()->setStatusCode($file->wasRecentlyCreated ? 201 : 200);
    }

    public function destroy(DiskFile $diskFile, DiskWriter $writer): Response
    {
        $writer->erase($diskFile);

        return response()->noContent();
    }
}

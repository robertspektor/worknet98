<?php

namespace App\Http\Controllers\Api\V1;

use App\FloppyDisks\ProgramInstaller;
use App\Models\DiskFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstalledProgramController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->player($request)->installedPrograms()->orderBy('installed_at')->orderBy('id')->pluck('program'),
        ]);
    }

    public function store(Request $request, DiskFile $diskFile, ProgramInstaller $installer): JsonResponse
    {
        $installed = $installer->install($this->player($request), $diskFile);

        return response()->json(['data' => $installed->program], $installed->wasRecentlyCreated ? 201 : 200);
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\UpdateNoteRequest;
use App\Notepad\NoteBook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NoteController extends ApiController
{
    public function show(Request $request, NoteBook $noteBook): JsonResponse
    {
        return response()->json(['data' => ['body' => $noteBook->noteOf($this->player($request))]]);
    }

    public function update(UpdateNoteRequest $request, NoteBook $noteBook): Response
    {
        $noteBook->write($this->player($request), $request->body());

        return response()->noContent();
    }
}

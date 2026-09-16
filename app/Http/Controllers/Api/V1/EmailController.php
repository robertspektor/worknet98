<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\EmailResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmailController extends ApiController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return EmailResource::collection($this->player($request)->emails()->latest('received_at')->latest('id')->get());
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\CustomerResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerController extends ApiController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return CustomerResource::collection($this->employment($request)->company->customers()->orderBy('name')->get());
    }
}

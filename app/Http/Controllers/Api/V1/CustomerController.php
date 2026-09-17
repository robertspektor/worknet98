<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\CustomerResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CustomerController extends ApiController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $customers = $this->employment($request)->branch()->customers()->with('person.household')->get();

        return CustomerResource::collection($customers->sortBy('person.name')->values());
    }
}

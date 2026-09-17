<?php

namespace App\Http\Controllers\Api\V1;

use App\CivilRegistry\RegistrySearch;
use App\Http\Requests\SearchRegistryRequest;
use App\Http\Resources\RegistryPersonResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RegistryPersonController extends ApiController
{
    public function index(SearchRegistryRequest $request, RegistrySearch $registry): AnonymousResourceCollection
    {
        return RegistryPersonResource::collection($registry->find($this->employment($request)->branch(), $request->search()));
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Careers\JobApplicationSubmitter;
use App\Http\Requests\StoreJobApplicationRequest;
use App\Http\Resources\JobApplicationResource;
use App\Models\JobOpening;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class JobApplicationController extends ApiController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return JobApplicationResource::collection($this->player($request)->jobApplications()->latest()->get());
    }

    public function store(StoreJobApplicationRequest $request, JobOpening $jobOpening, JobApplicationSubmitter $submitter): JsonResponse
    {
        $application = $submitter->submit($this->player($request), $jobOpening, $request->applicationMessage());

        return (new JobApplicationResource($application))->response()->setStatusCode(201);
    }
}

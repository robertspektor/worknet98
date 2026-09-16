<?php

namespace App\Http\Controllers\Api\V1;

use App\Game\ActionRefused;
use App\Http\Controllers\Controller;
use App\Models\Employment;
use App\Models\User;
use App\Work\ShiftRefusal;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

abstract class ApiController extends Controller
{
    protected function player(Request $request): User
    {
        return $request->user() ?? throw new AuthenticationException;
    }

    protected function employment(Request $request): Employment
    {
        return $this->player($request)->employment ?? throw new ActionRefused(ShiftRefusal::NotEmployed);
    }
}

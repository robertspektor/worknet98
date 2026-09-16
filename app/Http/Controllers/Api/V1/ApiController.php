<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

abstract class ApiController extends Controller
{
    protected function player(Request $request): User
    {
        return $request->user() ?? throw new AuthenticationException;
    }
}

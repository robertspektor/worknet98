<?php

use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\RecordLastSeen;
use App\Http\Middleware\RecordSession;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();

        $middleware->trimStrings(except: [
            fn (Request $request): bool => $request->is('api/v1/note', 'api/v1/floppy-disks/*/files'),
        ]);

        $middleware->api(append: [
            SetLocale::class,
            RecordLastSeen::class,
            RecordSession::class,
        ]);

        $middleware->web(append: [
            SetLocale::class,
            RecordLastSeen::class,
            RecordSession::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->redirectGuestsTo('/');
        $middleware->redirectUsersTo('/desk');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();

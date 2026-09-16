<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureRateLimits();
    }

    private function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(app()->isProduction());
    }

    private function configureRateLimits(): void
    {
        RateLimiter::for('login-links', fn (Request $request): array => [
            Limit::perMinute(3)->by('email:'.Str::lower($request->string('email')->toString())),
            Limit::perHour(20)->by('ip:'.$request->ip()),
        ]);

        RateLimiter::for('login-redemptions', fn (Request $request): Limit => Limit::perMinute(10)->by($request->ip()));
    }
}

<?php

namespace App\Providers;

use App\Models\FloppyDisk;
use App\Models\HardwarePart;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureRateLimits();
        $this->configureGates();
    }

    private function configureGates(): void
    {
        Gate::define('enter-citynet', fn (User $player): bool => $player->isMayor());
    }

    private function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(app()->isProduction());

        Relation::morphMap([
            'floppy_disk' => FloppyDisk::class,
            'hardware_part' => HardwarePart::class,
        ]);
    }

    private function configureRateLimits(): void
    {
        RateLimiter::for('sign-in', fn (Request $request): array => [
            Limit::perMinute(3)->by('email:'.Str::lower($request->string('email')->toString())),
            Limit::perHour(20)->by('ip:'.$request->ip()),
        ]);

        RateLimiter::for('login-redemptions', fn (Request $request): Limit => Limit::perMinute(10)->by($request->ip()));
    }
}

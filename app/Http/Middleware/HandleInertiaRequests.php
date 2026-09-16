<?php

namespace App\Http\Middleware;

use App\Http\Resources\PlayerResource;
use App\Localization\SupportedLocales;
use App\Localization\Translations;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * @var string
     */
    protected $rootView = 'app';

    public function __construct(
        private readonly SupportedLocales $locales,
        private readonly Translations $translations,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'player' => $user ? (new PlayerResource($user))->resolve($request) : null,
            'locale' => app()->getLocale(),
            'locales' => $this->locales->all(),
            'translations' => $this->translations->for(app()->getLocale()),
            'status' => $request->session()->get('status'),
        ];
    }
}

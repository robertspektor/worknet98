<?php

namespace App\Localization;

use App\Models\User;
use Illuminate\Http\Request;

class LocaleResolver
{
    public const SESSION_KEY = 'locale';

    public function __construct(private readonly SupportedLocales $locales) {}

    public function resolve(Request $request): string
    {
        return collect([
            $this->playerLocale($request),
            $this->sessionLocale($request),
            $request->getPreferredLanguage($this->locales->codes()),
        ])->first(fn (mixed $locale): bool => is_string($locale) && $this->locales->isSupported($locale))
            ?? $this->locales->fallback();
    }

    private function sessionLocale(Request $request): mixed
    {
        return $request->hasSession() ? $request->session()->get(self::SESSION_KEY) : null;
    }

    private function playerLocale(Request $request): ?string
    {
        $user = $request->user();

        return $user instanceof User ? $user->locale : null;
    }
}

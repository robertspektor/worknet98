<?php

namespace App\Localization;

class SupportedLocales
{
    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        /** @var array<string, string> */
        return config('localization.supported');
    }

    /**
     * @return list<string>
     */
    public function codes(): array
    {
        return array_keys($this->all());
    }

    public function isSupported(?string $locale): bool
    {
        return $locale !== null && array_key_exists($locale, $this->all());
    }

    public function fallback(): string
    {
        /** @var string */
        return config('app.fallback_locale');
    }
}

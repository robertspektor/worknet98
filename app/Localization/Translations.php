<?php

namespace App\Localization;

use Illuminate\Translation\Translator;

class Translations
{
    public function __construct(private readonly Translator $translator) {}

    /**
     * @return array<string, string>
     */
    public function for(string $locale): array
    {
        /** @var array<string, string> */
        return $this->translator->getLoader()->load($locale, '*', '*');
    }
}

<?php

namespace App\Localization;

use App\Models\User;
use Illuminate\Http\Request;

class LocaleSwitcher
{
    public function switch(Request $request, string $locale): void
    {
        $request->session()->put(LocaleResolver::SESSION_KEY, $locale);

        $user = $request->user();

        if ($user instanceof User) {
            $user->update(['locale' => $locale]);
        }

        app()->setLocale($locale);
    }
}

<?php

namespace App\Http\Controllers;

use App\Localization\SupportedLocales;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function show(Request $request, SupportedLocales $locales): View|RedirectResponse
    {
        if ($request->user() !== null) {
            return redirect()->route('home');
        }

        return view('public.landing', ['catalogue' => $this->catalogue($request, $locales)]);
    }

    /**
     * @return array<string, list<string>>
     */
    private function errors(Request $request): array
    {
        $errors = $request->session()->get('errors');

        return $errors === null ? [] : $errors->getBag('default')->getMessages();
    }

    /**
     * @return array<string, mixed>
     */
    private function catalogue(Request $request, SupportedLocales $locales): array
    {
        return [
            'action' => route('sign-in.store'),
            'csrf' => $request->session()->token(),
            'locale' => app()->getLocale(),
            'locales' => $locales->all(),
            'email' => old('email', ''),
            'ageConfirmed' => (bool) old('age_confirmed'),
            'errors' => array_map(fn (array $messages): string => $messages[0], $this->errors($request)),
            'status' => session('status') === 'login-link-sent' ? __('setup.sent_body') : null,
            'labels' => [
                'title' => __('landing.catalogue_title'),
                'heading' => __('landing.order_heading'),
                'note' => __('landing.catalogue_note'),
                'language' => __('setup.language'),
                'email' => __('setup.email'),
                'age' => __('setup.age_confirmed'),
                'submit' => __('landing.order_submit'),
            ],
        ];
    }
}

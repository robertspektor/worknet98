<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class LegalController extends Controller
{
    public function imprint(): View
    {
        return view('public.imprint');
    }

    public function privacy(): View
    {
        return view('public.privacy');
    }
}

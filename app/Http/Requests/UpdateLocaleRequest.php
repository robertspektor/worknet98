<?php

namespace App\Http\Requests;

use App\Localization\SupportedLocales;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLocaleRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(SupportedLocales $locales): array
    {
        return [
            'locale' => ['required', 'string', Rule::in($locales->codes())],
        ];
    }

    public function locale(): string
    {
        return $this->string('locale')->toString();
    }
}

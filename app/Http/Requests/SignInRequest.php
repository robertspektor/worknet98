<?php

namespace App\Http\Requests;

use App\Localization\SupportedLocales;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SignInRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(SupportedLocales $locales): array
    {
        return [
            'email' => ['required', 'string', 'email:rfc', 'max:255'],
            'age_confirmed' => ['accepted'],
            'locale' => ['required', 'string', Rule::in($locales->codes())],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.*' => __('setup.error_email'),
            'age_confirmed.accepted' => __('setup.error_age'),
        ];
    }

    public function email(): string
    {
        return $this->string('email')->trim()->toString();
    }

    public function locale(): string
    {
        return $this->string('locale')->toString();
    }
}

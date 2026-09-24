<?php

namespace App\Http\Requests;

use App\Localization\SupportedLocales;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginLinkRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(SupportedLocales $locales): array
    {
        return [
            'email' => ['required', 'string', 'email:rfc', 'max:255'],
            'locale' => ['required', 'string', Rule::in($locales->codes())],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return ['email.*' => __('terminal.error_email')];
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

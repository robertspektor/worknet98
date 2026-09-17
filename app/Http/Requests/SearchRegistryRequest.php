<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SearchRegistryRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['required', 'string', 'min:2', 'max:60'],
        ];
    }

    public function search(): string
    {
        return trim($this->string('search')->toString());
    }
}

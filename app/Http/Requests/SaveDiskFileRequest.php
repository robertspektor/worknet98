<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SaveDiskFileRequest extends FormRequest
{
    public const BODY_MAX_LENGTH = 10000;

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'regex:/^[A-Z0-9_-]{1,8}\.TXT$/'],
            'body' => ['present', 'nullable', 'string', 'max:'.self::BODY_MAX_LENGTH],
        ];
    }

    public function fileName(): string
    {
        return $this->string('name')->toString();
    }

    public function body(): string
    {
        return $this->string('body')->toString();
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('name'))) {
            $this->merge(['name' => mb_strtoupper(trim($this->input('name')))]);
        }
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateNoteRequest extends FormRequest
{
    public const BODY_MAX_LENGTH = 10000;

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'body' => ['present', 'nullable', 'string', 'max:'.self::BODY_MAX_LENGTH],
        ];
    }

    public function body(): string
    {
        return $this->string('body')->toString();
    }
}

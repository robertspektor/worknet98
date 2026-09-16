<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreJobApplicationRequest extends FormRequest
{
    public const MESSAGE_MAX_LENGTH = 1000;

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'message' => ['nullable', 'string', 'max:'.self::MESSAGE_MAX_LENGTH],
        ];
    }

    public function applicationMessage(): ?string
    {
        $message = $this->string('message')->trim()->toString();

        return $message === '' ? null : $message;
    }
}

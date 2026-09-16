<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ReplyToChatMessageRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reply' => ['required', 'string', 'max:64'],
        ];
    }

    public function replySlug(): string
    {
        return $this->string('reply')->toString();
    }
}

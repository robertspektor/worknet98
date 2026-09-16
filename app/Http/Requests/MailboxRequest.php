<?php

namespace App\Http\Requests;

use App\Mailbox\MailboxScope;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MailboxRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mailbox' => ['sometimes', Rule::enum(MailboxScope::class)],
        ];
    }

    public function scope(): MailboxScope
    {
        return $this->enum('mailbox', MailboxScope::class) ?? MailboxScope::Private;
    }
}

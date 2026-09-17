<?php

namespace App\Http\Requests;

use App\Mailbox\EmailAction;
use App\Mailbox\OutgoingEmail;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendWorkEmailRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $player = $this->user();
        $branchId = $player instanceof User ? $player->employment?->position->branch_id : null;

        return [
            'customer_id' => ['required', 'integer', Rule::exists('customers', 'id')->where('branch_id', $branchId)],
            'subject' => ['required', 'string', 'max:120'],
            'body' => ['required', 'string', 'max:2000'],
            'action' => ['required', Rule::enum(EmailAction::class)],
        ];
    }

    public function outgoingEmail(): OutgoingEmail
    {
        return new OutgoingEmail(
            recipient: Customer::query()->findOrFail($this->integer('customer_id')),
            subject: $this->string('subject')->trim()->toString(),
            body: $this->string('body')->trim()->toString(),
            action: $this->enum('action', EmailAction::class) ?? EmailAction::Other,
        );
    }
}

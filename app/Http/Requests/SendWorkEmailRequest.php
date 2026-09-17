<?php

namespace App\Http\Requests;

use App\Mailbox\EmailAction;
use App\Mailbox\EmailRecipient;
use App\Mailbox\OutgoingEmail;
use App\Models\Customer;
use App\Models\Position;
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
            'customer_id' => ['required_without:colleague_position_id', 'integer', Rule::exists('customers', 'id')->where('branch_id', $branchId)],
            'colleague_position_id' => ['required_without:customer_id', 'integer', Rule::exists('positions', 'id')->where('branch_id', $branchId)],
            'subject' => ['required', 'string', 'max:120'],
            'body' => ['required', 'string', 'max:2000'],
            'action' => ['required', Rule::enum(EmailAction::class)],
        ];
    }

    private function recipient(): EmailRecipient
    {
        return $this->has('colleague_position_id')
            ? EmailRecipient::colleague(Position::query()->with(['person', 'holder.user'])->findOrFail($this->integer('colleague_position_id')))
            : EmailRecipient::customer(Customer::query()->with('person')->findOrFail($this->integer('customer_id')));
    }

    public function outgoingEmail(): OutgoingEmail
    {
        return new OutgoingEmail(
            recipient: $this->recipient(),
            subject: $this->string('subject')->trim()->toString(),
            body: $this->string('body')->trim()->toString(),
            action: $this->enum('action', EmailAction::class) ?? EmailAction::Other,
        );
    }
}

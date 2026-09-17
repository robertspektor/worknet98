<?php

use App\Mailbox\EmailAction;
use App\Mailbox\EmailFolder;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Email;
use App\Models\Person;
use App\Models\Shift;

beforeEach(function () {
    $company = Company::factory()->create(['name' => 'Flowright Plumbing & Heating']);
    $branch = Branch::factory()->for($company)->create(['office_address' => 'office@flowright.wn']);
    $this->customer = Customer::factory()->for($branch)
        ->for(Person::factory()->named('Margaret Hollis')->state(['email_address' => 'm.hollis@mail.wn']))
        ->create();
    $this->player = playerOnDutyAt($branch);
});

it('sends a work mail to a customer with the chosen action', function () {
    $this->actingAs($this->player)
        ->postJson(route('api.v1.emails.store'), [
            'customer_id' => $this->customer->id,
            'subject' => 'Your appointment',
            'body' => 'Rita will come on Monday at 10:00.',
            'action' => 'confirm_appointment',
        ])
        ->assertCreated()
        ->assertJsonPath('data.folder', 'sent')
        ->assertJsonPath('data.recipient_address', 'm.hollis@mail.wn');

    $email = Email::sole();
    expect($email->folder)->toBe(EmailFolder::Sent)
        ->and($email->action)->toBe(EmailAction::ConfirmAppointment)
        ->and($email->employment_id)->toBe($this->player->employment?->id)
        ->and($email->sender_address)->toBe('office@flowright.wn');
});

it('refuses work mails while not on duty', function () {
    Shift::query()->update(['clocked_out_at' => now()]);

    $this->actingAs($this->player)
        ->postJson(route('api.v1.emails.store'), [
            'customer_id' => $this->customer->id,
            'subject' => 'Hello',
            'body' => 'Hello',
            'action' => 'other',
        ])
        ->assertUnprocessable()
        ->assertJsonPath('refusal', 'not_on_duty');
});

it('separates the private and the work mailbox', function () {
    Email::factory()->for($this->player)->create(['subject' => 'Private']);
    Email::factory()->for($this->player)->create(['subject' => 'Work', 'employment_id' => $this->player->employment?->id]);

    $this->actingAs($this->player)->getJson(route('api.v1.emails.index'))->assertJsonPath('data.*.subject', ['Private']);
    $this->actingAs($this->player)->getJson(route('api.v1.emails.index', ['mailbox' => 'work']))->assertJsonPath('data.*.subject', ['Work']);
});

<?php

namespace App\Mailbox;

use App\Models\Customer;
use App\Models\Employment;
use App\Models\Position;
use LogicException;

readonly class EmailRecipient
{
    public function __construct(
        public string $name,
        public string $address,
        public ?Employment $employment = null,
    ) {}

    public static function customer(Customer $customer): self
    {
        return new self(
            name: $customer->person->name,
            address: $customer->emailAddress() ?? throw new LogicException("Customer [{$customer->id}] has no e-mail address."),
        );
    }

    public static function colleague(Position $position): self
    {
        return new self(
            name: $position->person->name,
            address: $position->work_address,
            employment: $position->holder,
        );
    }
}

<?php

namespace App\Workplace;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Person;
use App\Models\Position;
use Random\Engine\Mt19937;
use Random\Randomizer;

class CustomerIntake
{
    public function customerFor(Branch $branch, Person $person): Customer
    {
        return Customer::query()->firstOrCreate(
            ['branch_id' => $branch->id, 'person_id' => $person->id],
            ['notes' => '', 'availability' => $this->availabilityOf($person)],
        );
    }

    public function businessContactFor(Branch $branch, Position $contact): Customer
    {
        return Customer::query()->firstOrCreate(
            ['branch_id' => $branch->id, 'person_id' => $contact->person_id],
            ['notes' => $contact->branch->company->name, 'contact_address' => $contact->work_address, 'availability' => Availability::Any],
        );
    }

    private function availabilityOf(Person $person): Availability
    {
        $availabilities = Availability::cases();

        return $availabilities[(new Randomizer(new Mt19937(crc32($person->slug))))->getInt(0, count($availabilities) - 1)];
    }
}

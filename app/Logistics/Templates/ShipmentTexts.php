<?php

namespace App\Logistics\Templates;

use App\Models\Customer;
use App\Models\Shipment;

readonly class ShipmentTexts
{
    public function __construct(
        private ShipmentTemplate $template,
        private Shipment $shipment,
        private Customer $contact,
    ) {}

    public function fill(string $text): string
    {
        return strtr($text, [
            ':contents' => $this->shipment->contents,
            ':size' => $this->template->sizeLabels[$this->shipment->size->value],
            ':sender' => $this->shipment->sender->company->name,
            ':contact' => $this->contact->person->name,
            ':recipient' => $this->shipment->recipient->company->name,
            ':district' => $this->shipment->recipient->name,
            ':due' => $this->shipment->dueAt()->settings(['locale' => $this->shipment->branch->company->locale])->isoFormat($this->template->dueFormat),
        ]);
    }

    /**
     * @param  'met'|'missed'  $result
     */
    public function feedback(string $aspect, string $result): string
    {
        return $this->fill($this->template->outcomeFeedback[$aspect][$result]);
    }
}

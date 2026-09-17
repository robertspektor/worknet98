<?php

namespace App\Cases\Templates;

use App\Models\Customer;

readonly class TemplateTexts
{
    public function __construct(
        private CaseTemplate $template,
        private Customer $customer,
    ) {}

    public function fill(string $text): string
    {
        return strtr($text, [
            ':customer' => $this->customer->person->name,
            ':availability' => $this->template->availabilityNotes[$this->customer->availability->value],
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

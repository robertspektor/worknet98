<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LabelFloppyDiskRequest extends FormRequest
{
    public const LABEL_MAX_LENGTH = 24;

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'label' => ['present', 'nullable', 'string', 'max:'.self::LABEL_MAX_LENGTH],
        ];
    }

    public function label(): ?string
    {
        $label = trim($this->string('label')->toString());

        return $label === '' ? null : $label;
    }
}

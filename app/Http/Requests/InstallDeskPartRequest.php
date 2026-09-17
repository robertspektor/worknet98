<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class InstallDeskPartRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'thermal_paste_applied' => ['required', 'boolean'],
        ];
    }

    public function thermalPasteApplied(): bool
    {
        return $this->boolean('thermal_paste_applied');
    }
}

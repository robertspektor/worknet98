<?php

namespace App\Http\Requests;

use App\CivilRegistry\ApplicationDecision;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DecideCivilApplicationRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'decision' => ['required', Rule::enum(ApplicationDecision::class)],
        ];
    }

    public function decision(): ApplicationDecision
    {
        return ApplicationDecision::from($this->string('decision')->toString());
    }
}

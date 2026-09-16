<?php

namespace App\Http\Requests;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCalendarEntryRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'time' => ['required', 'date_format:H:i'],
            'title' => ['required', 'string', 'max:80'],
        ];
    }

    public function entryDate(): CarbonImmutable
    {
        return CarbonImmutable::createFromFormat('Y-m-d', $this->string('date')->toString())?->startOfDay() ?? CarbonImmutable::today();
    }

    public function entryTime(): string
    {
        return $this->string('time')->toString();
    }

    public function entryTitle(): string
    {
        return $this->string('title')->trim()->toString();
    }
}

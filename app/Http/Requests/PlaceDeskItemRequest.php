<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class PlaceDeskItemRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'item' => ['required', 'string', 'max:40', 'regex:/^[a-z][a-z0-9-]*$/'],
            'x' => ['required', 'numeric', 'between:0,1'],
            'y' => ['required', 'numeric', 'between:0,1'],
        ];
    }

    public function item(): string
    {
        return $this->string('item')->toString();
    }

    public function x(): float
    {
        return $this->float('x');
    }

    public function y(): float
    {
        return $this->float('y');
    }
}

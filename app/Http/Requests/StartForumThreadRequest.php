<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StartForumThreadRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:120'],
            'body' => ['required', 'string', 'min:3', 'max:2000'],
        ];
    }

    public function title(): string
    {
        return $this->string('title')->trim()->toString();
    }

    public function body(): string
    {
        return $this->string('body')->trim()->toString();
    }
}

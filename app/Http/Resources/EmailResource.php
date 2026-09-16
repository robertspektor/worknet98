<?php

namespace App\Http\Resources;

use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Email
 */
class EmailResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sender_name' => $this->sender_name,
            'sender_address' => $this->sender_address,
            'subject' => $this->subject,
            'body' => $this->body,
            'received_at' => $this->received_at->toIso8601String(),
            'is_read' => $this->read_at !== null,
        ];
    }
}

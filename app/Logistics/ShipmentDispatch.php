<?php

namespace App\Logistics;

use App\Logistics\Templates\ShipmentTemplateCatalog;
use App\Models\Branch;
use App\Models\City;
use App\Models\Position;
use App\Models\Shipment;
use App\World\Events\ServiceProviders;
use Illuminate\Support\Facades\DB;

class ShipmentDispatch
{
    public const CARRIER_SERVICE = 'freight';

    public function __construct(
        private readonly ServiceProviders $providers,
        private readonly ShipmentTemplateCatalog $templates,
        private readonly ShipmentCaseOpener $opener,
    ) {}

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function send(City $city, Branch $sender, Branch $recipient, string $templateSlug, array $attributes, ?Position $assignee = null): ?Shipment
    {
        $carrier = $this->providers->branchFor($city, self::CARRIER_SERVICE);
        $template = $carrier === null ? null : $this->templates->find($carrier->company, $templateSlug);

        if ($carrier === null || $template === null) {
            return null;
        }

        return DB::transaction(function () use ($carrier, $sender, $recipient, $template, $attributes, $assignee): Shipment {
            $shipment = Shipment::create([
                ...$attributes,
                'branch_id' => $carrier->id,
                'sender_branch_id' => $sender->id,
                'recipient_branch_id' => $recipient->id,
            ]);

            $this->opener->open($shipment, $template, $assignee);

            return $shipment;
        });
    }
}

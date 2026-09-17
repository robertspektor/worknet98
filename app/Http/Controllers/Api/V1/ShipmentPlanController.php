<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\PlanShipmentRequest;
use App\Http\Resources\ShipmentResource;
use App\Logistics\TourPlanner;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ShipmentPlanController extends ApiController
{
    public function update(PlanShipmentRequest $request, Shipment $shipment, TourPlanner $planner): ShipmentResource
    {
        $planned = $planner->plan($this->player($request), $request->plan($shipment));

        return new ShipmentResource($planned->load(['sender.company', 'recipient.company', 'driver.person', 'plannedBy.position', 'dispatchCase.customer.person']));
    }

    public function destroy(Request $request, Shipment $shipment, TourPlanner $planner): Response
    {
        $planner->unplan($this->player($request), $shipment);

        return response()->noContent();
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\BookAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Workplace\AppointmentBooker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AppointmentController extends ApiController
{
    public function store(BookAppointmentRequest $request, AppointmentBooker $booker): JsonResponse
    {
        $appointment = $booker->book($this->player($request), $request->appointment());

        return (new AppointmentResource($appointment->load('customer')))->response()->setStatusCode(201);
    }

    public function destroy(Request $request, Appointment $appointment, AppointmentBooker $booker): Response
    {
        $booker->cancel($this->player($request), $appointment);

        return response()->noContent();
    }
}

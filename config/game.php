<?php

return [

    /*
    | Seconds a company takes to answer a job application. Answers are
    | delivered by the scheduled careers:review-applications command.
    */

    'job_application_response_delay_seconds' => (int) env('GAME_JOB_APPLICATION_RESPONSE_DELAY_SECONDS', 60),

    /*
    | Seconds until an ordered floppy disk arrives as a parcel. Leave empty
    | to deliver on the next game day. Parcels are delivered by the
    | scheduled shop:deliver-parcels command.
    */

    'parcel_delivery_delay_seconds' => env('GAME_PARCEL_DELIVERY_DELAY_SECONDS'),

];

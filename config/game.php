<?php

return [

    /*
    | Seconds a company takes to answer a job application. Answers are
    | delivered by the scheduled careers:review-applications command.
    */

    'job_application_response_delay_seconds' => (int) env('GAME_JOB_APPLICATION_RESPONSE_DELAY_SECONDS', 60),

];

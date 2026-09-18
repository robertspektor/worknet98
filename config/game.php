<?php

return [

    'mayor_name' => env('GAME_MAYOR_NAME', 'Bill Rixx'),

    /*
    | The game clock runs faster than real time. Game time starts at the
    | game epoch when real time is at the real epoch. With a scale of 4.35,
    | one real week is about one game month.
    */

    'clock' => [
        'scale' => (float) env('GAME_CLOCK_SCALE', 4.35),
        'real_epoch' => env('GAME_CLOCK_REAL_EPOCH', '2026-09-14 00:00:00'),
        'game_epoch' => env('GAME_CLOCK_GAME_EPOCH', '1998-01-05 00:00:00'),
    ],

    /*
    | Working time: every employment has a target of active minutes per
    | game month. Heartbeats further apart than the gap are not counted,
    | shifts without activity for the idle minutes are clocked out.
    */

    'work' => [
        'target_minutes' => (int) env('GAME_WORK_TARGET_MINUTES', 120),
        'heartbeat_gap_seconds' => (int) env('GAME_WORK_HEARTBEAT_GAP_SECONDS', 90),
        'idle_clock_out_minutes' => (int) env('GAME_WORK_IDLE_CLOCK_OUT_MINUTES', 15),
    ],

    /*
    | Seconds a company takes to answer a job application. Answers are
    | delivered by the scheduled careers:review-applications command.
    */

    'job_application_response_delay_seconds' => (int) env('GAME_JOB_APPLICATION_RESPONSE_DELAY_SECONDS', 60),

    /*
    | Seconds an NPC takes to work on a case routed to their position. Due
    | cases are worked by the scheduled cases:work-npc-cases command.
    */

    'npc_case_delay_seconds' => (int) env('GAME_NPC_CASE_DELAY_SECONDS', 120),

    /*
    | Hours after which an NPC colleague takes over a case that the player
    | has not booked, even if the player never worked another shift.
    */

    'case_takeover_after_hours' => (int) env('GAME_CASE_TAKEOVER_AFTER_HOURS', 48),

    /*
    | Hours without any activity after which a player counts as away. Cases
    | of an away player are handed over quietly, without a reliability hit,
    | and coming back brings the welcome back digest.
    */

    'absence_after_hours' => (int) env('GAME_ABSENCE_AFTER_HOURS', 20),

    /*
    | Seconds until an ordered floppy disk arrives as a parcel. Leave empty
    | to deliver on the next game day. Parcels are delivered by the
    | scheduled shop:deliver-parcels command.
    */

    'parcel_delivery_delay_seconds' => env('GAME_PARCEL_DELIVERY_DELAY_SECONDS'),

];

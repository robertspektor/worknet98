<?php

namespace App\Metrics;

enum FunnelStep: string
{
    case DesktopReached = 'desktop_reached';
    case WorkNetOpened = 'worknet_opened';
    case ApplicationSent = 'application_sent';
}

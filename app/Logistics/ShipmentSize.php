<?php

namespace App\Logistics;

enum ShipmentSize: string
{
    case Parcel = 'parcel';
    case Pallet = 'pallet';
}

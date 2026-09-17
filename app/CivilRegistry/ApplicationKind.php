<?php

namespace App\CivilRegistry;

enum ApplicationKind: string
{
    case Move = 'move';
    case Marriage = 'marriage';
    case PetRegistration = 'pet_registration';
    case NameChange = 'name_change';
}

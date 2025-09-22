<?php

namespace App\Enums;

use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Names;
use ArchTech\Enums\Values;

enum UserPermissionsEnum: string
{
    use InvokableCases, Values, Names;
        
    case ADMIN    = 'admin';
    case CUSTOMER = 'customer';
    case VENDOR   = 'vendor';
    case QA       = 'qa';
    case ACCOUNT  = 'account';
}
<?php

namespace App\Entity\Enum;

use ValueError;
use App\Entity\User;

enum TypeImageEnum: string
{
    case USER = 'User';
    case PRODUCT = 'Product';
    case BANNER = 'Banner';
    case COVER = 'Cover';
}
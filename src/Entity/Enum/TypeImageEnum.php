<?php

namespace App\Entity\Enum;

use ValueError;
use App\Entity\User;

enum TypeImageEnum: string
{
    case USER = User::class;
    case PRODUCT = 'product';
    case BANNER = 'banner';
    case COVER = 'cover';
}
<?php

namespace App\Entity\Enum;

enum TypeImageEnum: string
{
    case AVATAR = 'avatar';
    case PRODUCT = 'product';
    case BANNER = 'banner';
    case COVER = 'cover';
}
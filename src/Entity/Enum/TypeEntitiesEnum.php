<?php

namespace App\Entity\Enum;

use App\Entity\User;
use App\Entity\Role;
use App\Entity\Image;
use App\Entity\Login;

enum TypeEntitiesEnum: string
{
    case USER = User::class;
    case ROLE = Role::class;
    case IMAGE = Image::class;
    case LOGIN = Login::class;
}


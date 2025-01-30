<?php

namespace App\Entity\Enum;

enum TypeImageExtensionEnum: string
{
    case PNG = 'png';
    case JPG = 'jpg';
    case JPEG = 'jpeg';
    case GIF = 'gif';
    case BMP = 'bmp';
    case WEBP = 'webp';
}


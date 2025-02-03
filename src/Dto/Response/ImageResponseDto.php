<?php

namespace App\Dto\Response;

use App\Entity\Image;

class ImageResponseDto
{
    public string $id;
    public string $name;
    public string $path;
    public string $typeImage;
    public string $typeImageExtension;
    public string $ownerClass;
    public string $ownerId;
}

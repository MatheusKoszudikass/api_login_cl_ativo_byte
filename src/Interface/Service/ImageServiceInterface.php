<?php

namespace App\Interface\Service;

use App\Entity\Enum\TypeImageEnum;
use App\Entity\Image;
use App\Dto\Create\ImageCreateDto;
use App\Dto\DoctrineFindParams;
use App\Result\ResultOperation;


interface ImageServiceInterface
{
    public function saveImage(ImageCreateDto $imageCreateDto): ResultOperation;
    public function deleteImageById(TypeImageEnum $typeImageEnum , DoctrineFindParams $identitier): ResultOperation;
    public function getImage(TypeImageEnum $typeImageEnum , DoctrineFindParams $identitier): ?ResultOperation;
    public function getImageAll(): ResultOperation;
    public function uploadImage(ImageCreateDto $imageCreateDto, DoctrineFindParams $identitier): ResultOperation;
}

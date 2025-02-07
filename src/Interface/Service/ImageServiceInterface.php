<?php

namespace App\Interface\Service;

use App\Entity\Enum\TypeImageEnum;
use App\Dto\Create\ImageCreateDto;
use App\Util\DoctrineFindParams;
use App\Util\ResultOperation;


interface ImageServiceInterface
{
    public function saveImage(ImageCreateDto $imageCreateDto): ResultOperation;
    public function getImage(TypeImageEnum $typeImageEnum , DoctrineFindParams $criteria): ResultOperation;
    public function getImageAll(DoctrineFindParams $criteria, int $page, int $size): ResultOperation;
    public function getImageOneBy(DoctrineFindParams $criteria): ResultOperation;
    public function getImagesBy(DoctrineFindParams $criteria): ResultOperation;
    public function uploadImage(ImageCreateDto $imageCreateDto, DoctrineFindParams $criteria): ResultOperation;
    public function deleteImageBy(TypeImageEnum $typeImageEnum , DoctrineFindParams $criteria): ResultOperation;
}

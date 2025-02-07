<?php

namespace Tests\DataFixtures\Entity;

use App\Dto\Create\ImageCreateDto;
use App\Dto\Response\ImageResponseDto;
use App\Entity\Image;
use App\Entity\Enum\TypeImageEnum;

class ImageDataTest
{
    public static function createImage(): Image
    {
        return new Image(
            'user1',
            'fake_path/user.png',
            TypeImageEnum::USER,
            'User',
            '1'
        );
    }

    public static function createProductImage(): Image
    {
        return new Image(
            'product1',
            'fake_path/product.jpg',
            TypeImageEnum::PRODUCT,
            'Product',
            '2'
        );
    }

    public static function createBannerImage(): Image
    {
        return new Image(
            'banner1',
            'fake_path/banner.jpeg',
            TypeImageEnum::BANNER,
            'Advertisement',
            '3'
        );
    }

    public static function createCoverImage(): Image
    {
        return new Image(
            'cover1',
            'fake_path/cover.gif',
            TypeImageEnum::COVER,
            'Book',
            '4'
        );
    }

    public static function createUserImage(): Image
    {
        return new Image(
            'user2',
            'fake_path/user.bmp',
            TypeImageEnum::USER,
            'User',
            '5'
        );
    }

    public static function createProduct1Image(): Image
    {
        return new Image(
            'product1',
            'fake_path/product.webp',
            TypeImageEnum::PRODUCT,
            'Product',
            '6'
        );
    }

    public static function createExceptionImage(): Image
    {
        return new Image(
            '',
            '',
            TypeImageEnum::USER,
            '',
            '',
        );
    }

    public static function createUserImageDto(): ImageCreateDto
    {
        $dto = new ImageCreateDto();
        $dto->name = 'image1';
        $dto->path = 'fake_path/image1.jpg';
        $dto->typeImage = TypeImageEnum::USER;
        $dto->ownerClass = 'User';
        $dto->ownerId = '1';

        return $dto;
    }

    public static function updateUserImageDto(): ImageCreateDto
    {
        $dto = new ImageCreateDto();
        $dto->name = 'image1 edit';
        $dto->path = 'fake_path/image1edit.jpg';
        $dto->typeImage = TypeImageEnum::USER;
        $dto->ownerClass = 'User';
        $dto->ownerId = '2';

        return $dto;
    }
}


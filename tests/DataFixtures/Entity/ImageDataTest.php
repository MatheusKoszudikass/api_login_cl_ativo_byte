<?php

namespace Tests\DataFixtures\Entity;

use App\Entity\Image;
use App\Entity\Enum\TypeImageEnum;

class ImageDataTest
{
    public static function createImage(): Image
    {
        return new Image(
            'avatar1',
            'fake_path/avatar.png',
            TypeImageEnum::AVATAR,
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

    public static function createAvatarImage(): Image
    {
        return new Image(
            'avatar2',
            'fake_path/avatar.bmp',
            TypeImageEnum::AVATAR,
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
}


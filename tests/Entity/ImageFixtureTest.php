<?php

namespace Tests\Entity;

use App\Entity\Enum\TypeImageExtensionEnum;
use App\Entity\Image;
use Tests\DataFixtures\Entity\ImageDataTest;
use App\Entity\Enum\TypeImageEnum;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;

class ImageFixtureTest extends KernelTestCase
{
    public function testCreateImageTeste(): void
    {
        $image = ImageDataTest::createImage();
        assertInstanceOf(Image::class, $image);
        assertEquals('avatar1', $image->getName());
        assertEquals('fake_path/avatar.png', $image->getPath());
        assertEquals(TypeImageEnum::AVATAR, $image->getTypeImage());
        assertEquals('User', $image->getOwnerClass());
        assertEquals('1', $image->getOwnerId());
        assertEquals(TypeImageExtensionEnum::PNG, $image->getTypeImageExtension());
    }

    public function testCreateImageProductTeste(): void
    {
        $image = ImageDataTest::createProductImage();
        assertInstanceOf(Image::class, $image);
        assertEquals('product1', $image->getName());
        assertEquals('fake_path/product.jpg', $image->getPath());
        assertEquals(TypeImageEnum::PRODUCT, $image->getTypeImage());
        assertEquals('Product', $image->getOwnerClass());
        assertEquals('2', $image->getOwnerId());
        assertEquals(TypeImageExtensionEnum::JPG, $image->getTypeImageExtension());
    }

    public function testCreateImageBannerTeste(): void
    {
        $image = ImageDataTest::createBannerImage();
        assertInstanceOf(Image::class, $image);
        assertEquals('banner1', $image->getName());
        assertEquals('fake_path/banner.jpeg', $image->getPath());
        assertEquals(TypeImageEnum::BANNER, $image->getTypeImage());
        assertEquals('Advertisement', $image->getOwnerClass());
        assertEquals('3', $image->getOwnerId());
        assertEquals(TypeImageExtensionEnum::JPEG, $image->getTypeImageExtension());
    }

    public function testCreateImageCoverTeste(): void
    {
        $image = ImageDataTest::createCoverImage();
        assertInstanceOf(Image::class, $image);
        assertEquals('cover1', $image->getName());
        assertEquals('fake_path/cover.gif', $image->getPath());
        assertEquals(TypeImageEnum::COVER, $image->getTypeImage());
        assertEquals('Book', $image->getOwnerClass());
        assertEquals('4', $image->getOwnerId());
        assertEquals(TypeImageExtensionEnum::GIF, $image->getTypeImageExtension());
    }

    public function testCreateImageAvatar1Test(): void
    {
        $image = ImageDataTest::createAvatarImage();
        assertInstanceOf(Image::class, $image);
        assertEquals('avatar2', $image->getName());
        assertEquals('fake_path/avatar.bmp', $image->getPath());
        assertEquals(TypeImageEnum::AVATAR, $image->getTypeImage());
        assertEquals('User', $image->getOwnerClass());
        assertEquals('5', $image->getOwnerId());
        assertEquals(TypeImageExtensionEnum::BMP, $image->getTypeImageExtension()); 
    }

    public function testCreateImageProduct1Test(): void
    {
        $image = ImageDataTest::createProduct1Image();
        assertInstanceOf(Image::class, $image);
        assertEquals('product1', $image->getName());
        assertEquals('fake_path/product.webp', $image->getPath());
        assertEquals(TypeImageEnum::PRODUCT, $image->getTypeImage());
        assertEquals('Product', $image->getOwnerClass());
        assertEquals('6', $image->getOwnerId());
        assertEquals(TypeImageExtensionEnum::WEBP, $image->getTypeImageExtension());
    }
}

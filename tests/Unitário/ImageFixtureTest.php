<?php

namespace Tests\Entity;

use App\Entity\Enum\TypeImageExtensionEnum;
use App\Entity\Enum\TypeImageEnum;
use App\Entity\Image;
use Tests\DataFixtures\Entity\ImageDataTest;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use InvalidArgumentException;
use ValueError;

class ImageFixtureTest extends KernelTestCase
{
    public function testCreateImage(): void
    {
        $image = ImageDataTest::createImage();
        $this->assertInstanceOf(Image::class, $image);
        $this->assertEquals('user1', $image->getName());
        $this->assertEquals('fake_path/user.png', $image->getPath());
        $this->assertEquals(TypeImageEnum::USER, $image->getTypeImage());
        $this->assertEquals('User', $image->getOwnerClass());
        $this->assertEquals('1', $image->getOwnerId());
        $this->assertEquals(TypeImageExtensionEnum::PNG, $image->getTypeImageExtension());
    }

    public function testCreateImageProduct(): void
    {
        $image = ImageDataTest::createProductImage();
        $this->assertInstanceOf(Image::class, $image);
        $this->assertEquals('product1', $image->getName());
        $this->assertEquals('fake_path/product.jpg', $image->getPath());
        $this->assertEquals(TypeImageEnum::PRODUCT, $image->getTypeImage());
        $this->assertEquals('Product', $image->getOwnerClass());
        $this->assertEquals('2', $image->getOwnerId());
        $this->assertEquals(TypeImageExtensionEnum::JPG, $image->getTypeImageExtension());
    }

    public function testCreateImageBanner(): void
    {
        $image = ImageDataTest::createBannerImage();
        $this->assertInstanceOf(Image::class, $image);
        $this->assertEquals('banner1', $image->getName());
        $this->assertEquals('fake_path/banner.jpeg', $image->getPath());
        $this->assertEquals(TypeImageEnum::BANNER, $image->getTypeImage());
        $this->assertEquals('Advertisement', $image->getOwnerClass());
        $this->assertEquals('3', $image->getOwnerId());
        $this->assertEquals(TypeImageExtensionEnum::JPEG, $image->getTypeImageExtension());
    }

    public function testCreateImageCover(): void
    {
        $image = ImageDataTest::createCoverImage();
        $this->assertInstanceOf(Image::class, $image);
        $this->assertEquals('cover1', $image->getName());
        $this->assertEquals('fake_path/cover.gif', $image->getPath());
        $this->assertEquals(TypeImageEnum::COVER, $image->getTypeImage());
        $this->assertEquals('Book', $image->getOwnerClass());
        $this->assertEquals('4', $image->getOwnerId());
        $this->assertEquals(TypeImageExtensionEnum::GIF, $image->getTypeImageExtension());
    }

    public function testCreateImageAvatar1Test(): void
    {
        $image = ImageDataTest::createUserImage();
        $this->assertInstanceOf(Image::class, $image);
        $this->assertEquals('user2', $image->getName());
        $this->assertEquals('fake_path/user.bmp', $image->getPath());
        $this->assertEquals(TypeImageEnum::USER, $image->getTypeImage());
        $this->assertEquals('User', $image->getOwnerClass());
        $this->assertEquals('5', $image->getOwnerId());
        $this->assertEquals(TypeImageExtensionEnum::BMP, $image->getTypeImageExtension()); 
    }

    public function testCreateImageProduct1Test(): void
    {
        $image = ImageDataTest::createProduct1Image();
        $this->assertInstanceOf(Image::class, $image);
        $this->assertEquals('product1', $image->getName());
        $this->assertEquals('fake_path/product.webp', $image->getPath());
        $this->assertEquals(TypeImageEnum::PRODUCT, $image->getTypeImage());
        $this->assertEquals('Product', $image->getOwnerClass());
        $this->assertEquals('6', $image->getOwnerId());
        $this->assertEquals(TypeImageExtensionEnum::WEBP, $image->getTypeImageExtension());
    }

    public function testCreateImageNameException(): void 
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("O nome da imagem não pode ser vazio.");
        ImageDataTest::createUserImage()->setName('');
    }

    public function testCreateImagePathException(): void 
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("O campo 'path' não pode ser vazio.");
        ImageDataTest::createUserImage()->setPath('');
    }

    public function testCreateImageTypeImageException(): void 
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage( '"" is not a valid backing');
        ImageDataTest::createUserImage()->setTypeImage(TypeImageEnum::from(''));
    }

    public function testCreateImageTypeImageExtensionException(): void 
    {
        $this->expectException(ValueError::class);
        $this->expectExceptionMessage( '"" is not a valid backing');
        ImageDataTest::createUserImage()->setTypeImageExtension(TypeImageExtensionEnum::from(''));
    }

    public function testCreateImageOwnerCalssException(): void 
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("O campo 'ownerClass' não pode ser vazio.");
        ImageDataTest::createUserImage()->setOwnerClass('');
    }

    public function testCreateImageOwneridException(): void
    {
         $this->expectException(InvalidArgumentException::class);
         $this->expectExceptionMessage("O campo 'ownerId' não pode ser vazio.");
         ImageDataTest::createUserImage()->setOwnerId('');
    }
}

<?php

namespace Tests\Repository\Image;

use App\Dto\Create\ImageCreateDto;
use App\Dto\Response\ImageResponseDto;
use App\Dto\DoctrineFindParams;
use App\Entity\Enum\TypeImageEnum;
use App\Repository\ImageRepository;
use App\Service\ImageService;
use App\Service\MapperServiceCreate;
use App\Service\MapperServiceResponse;
use Tests\DataFixtures\Entity\ImageDataTest;
use Tests\Dependency\Image\ImageDependencies;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ImageServiceTest extends KernelTestCase
{
    private ImageService $_imageService;
    private ImageDependencies $_imageDependencies;

    protected function setUp(): void
    {
        $this->_imageDependencies = new ImageDependencies();
        $this->_imageService =  $this->_imageDependencies->imageService(); 
    }

    public function testImageService(): void 
    {
        $this->assertInstanceOf(ImageService::class, $this->_imageService);

        $result = $this->_imageService->saveImage(ImageDataTest::createUserImageDto());

        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('Imagem salva com sucesso.', $result->getMessage());

        self::testGetImage();
    }

    private function testGetImage(): void
    {
        $params = new DoctrineFindParams('name', 
        ImageDataTest::createUserImageDto()->name, ImageDataTest::createUserImageDto()->typeImage);

        $result = $this->_imageDependencies::getImageByTest($params);

        $params = new DoctrineFindParams(
            'id', $result->getId(), $result->getTypeImage());

        $result = $this->_imageService->getImage(TypeImageEnum::USER, $params);

        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('Imagem encontrada com sucesso.', $result->getMessage());

        self::testGetImageAll();
    }


    private function testGetImageAll(): void 
    {
        $result = $this->_imageService->getImageAll();

        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('Imagem encontrada com sucesso.', $result->getMessage());

        self::testUpdateImageById();
    }

    private function testUpdateImageById(): void
    {
        $params = new DoctrineFindParams('name', ImageDataTest::createUserImageDto()->name, 
            ImageDataTest::createUserImageDto()->typeImage);

        $result = $this->_imageDependencies::getImageByTest($params);

        $params = new DoctrineFindParams(
            'id', $result->getId(), $result->getTypeImage());

        $image = ImageDataTest::updateUserImageDto();

        $result = $this->_imageService->uploadImage($image, $params);

        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('Imagem atualizada com sucesso.', $result->getMessage());
        
        self::testDeleteImageById();
    }

    private function testDeleteImageById(): void
    {
        $params = new DoctrineFindParams(
            'name', ImageDataTest::updateUserImageDto()->name,
            ImageDataTest::updateUserImageDto()->typeImage);

        $result = $this->_imageDependencies::getImageByTest($params);

        $params = new DoctrineFindParams(
            'id', $result->getId(), ImageDataTest::createUserImageDto()->typeImage);

        $result = $this->_imageService->deleteImageById(TypeImageEnum::USER, $params);

        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('Imagem deletada com sucesso.', $result->getMessage());   
    }
}
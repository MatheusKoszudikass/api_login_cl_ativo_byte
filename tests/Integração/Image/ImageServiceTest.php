<?php

namespace Tests\Repository\Image;

use App\Dto\Create\ImageCreateDto;
use App\Entity\Enum\TypeEntitiesEnum;
use App\Util\DoctrineFindParams;
use App\Entity\Enum\TypeImageEnum;
use App\Service\Image\ImageService;
use Doctrine\DBAL\Types\Type;
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

    public function testSaveImage(): void 
    {
        $this->assertInstanceOf(ImageService::class, $this->_imageService);

        $result = $this->_imageService->saveImage(new ImageCreateDto());

        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Objeto imagem não pode ser inválido.', $result->getMessage());

        $result = $this->_imageService->saveImage(ImageDataTest::createUserImageDto());

        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('Imagem salva com sucesso.', $result->getMessage());

        self::testGetImage();
    }

    private function testGetImage(): void
    {
        $params = new DoctrineFindParams(
            'name', ImageDataTest::createUserImageDto()->name, 
             TypeEntitiesEnum::IMAGE);

        $result = $this->_imageDependencies::getImageByTest($params);
        
        $params = new DoctrineFindParams('id', $result->getId(),
            TypeEntitiesEnum::IMAGE);

        $result = $this->_imageService->getImage(
            TypeImageEnum::PRODUCT,
            new DoctrineFindParams('', '', TypeEntitiesEnum::IMAGE));

        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Tipo de imagem não pode ser inválido.', $result->getMessage());

        $result = $this->_imageService->getImage(TypeImageEnum::USER, $params);

        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('Imagem encontrada com sucesso.', $result->getMessage());

        self::testGetImageAll();
    }

    private function testGetImageAll(): void 
    {
        $parms = new DoctrineFindParams(
            '', '', TypeEntitiesEnum::IMAGE
        );

        $result = $this->_imageService->getImageAll($parms, 1, 10);

        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('Imagens encontradas com sucesso.', $result->getMessage());

        self::testGetImageOneBy();
    }

    private function testGetImageOneBy(): void
    {
        $params = new DoctrineFindParams(
            'name', ImageDataTest::createUserImageDto()->name, 
            TypeEntitiesEnum::IMAGE);
        $result = $this->_imageService->getImageOneBy($params);
        
        
        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());

        self::testGetImagesBy();
    }

    private function testGetImagesBy(): void
    {
        $result = $this->_imageService->getImageOneBy(
            new DoctrineFindParams('id', '',
            TypeEntitiesEnum::IMAGE));

        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertEquals('Parâmetro de busca inválido.', $result->getMessage());

        $params = new DoctrineFindParams(
            'name', ImageDataTest::createUserImageDto()->name,
            TypeEntitiesEnum::IMAGE);

        $result = $this->_imageService->getImagesBy($params);

        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());

        self::testUploadImage();
    }

    private function testUploadImage(): void
    {
        $result = $this->_imageService->uploadImage(
            ImageDataTest::createUserImageDto(),
            new DoctrineFindParams('', '', TypeEntitiesEnum::IMAGE)
        );
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertEquals('Objeto imagem não pode ser inválido.', $result->getMessage());
        
        $params = new DoctrineFindParams(
            'name', ImageDataTest::createUserImageDto()->name, 
            TypeEntitiesEnum::IMAGE);

        $result = $this->_imageDependencies::getImageByTest($params);

        $params = new DoctrineFindParams('id', $result->getId(), 
        TypeEntitiesEnum::IMAGE);

        $result = $this->_imageService->uploadImage(
            new ImageCreateDto(),
            $params
        );

        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertEquals('Objeto imagem não pode ser inválido.', $result->getMessage());

        $result = $this->_imageService->uploadImage(
            ImageDataTest::updateUserImageDto(), $params);

        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('Imagem atualizada com sucesso.', $result->getMessage());

        self::testDeleteImageBy();
    }

    private function testDeleteImageBy(): void
    {
        $params = new DoctrineFindParams(
        'name', ImageDataTest::updateUserImageDto()->name,
            TypeEntitiesEnum::IMAGE);

        $result = $this->_imageService->deleteImageBy(
            TypeImageEnum::from("User"), new DoctrineFindParams(
                '', '', TypeEntitiesEnum::IMAGE));
        
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Tipo de imagem não pode ser inválido.', $result->getMessage());

        $result = $this->_imageDependencies::getImageByTest($params);

        $params = new DoctrineFindParams('id', $result->getId(),
            TypeEntitiesEnum::IMAGE);
        
        $result = $this->_imageService->deleteImageBy(TypeImageEnum::USER, $params);

        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('Imagem deletada com sucesso.', $result->getMessage());
    }
}

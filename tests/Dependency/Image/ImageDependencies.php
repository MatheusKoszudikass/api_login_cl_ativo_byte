<?php

namespace Tests\Dependency\Image;

use App\Dto\DoctrineFindParams;
use App\Entity\Image;
use App\Service\ImageService;
use App\Service\MapperServiceCreate;
use App\Repository\ImageRepository;
use App\Interface\ImageRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Dependency\DatabaseTestCase;

class ImageDependencies extends KernelTestCase
{
    public function imageService(): ImageService
    {
        $dataBase = new DatabaseTestCase();
        $dataBase->setUp();
        $container = static::getContainer();

        $imageRepositoryInterface = $container->get(ImageRepositoryInterface::class);
        $mapperServiceCreate = $container->get(MapperServiceCreate::class);


        return new ImageService(
            $imageRepositoryInterface,
            $mapperServiceCreate
        );
    }

    public function imageRepository(): ImageRepository
    {
        $container = static::getContainer();
        $entityManager = $container->get(ManagerRegistry::class);

        return new ImageRepository($entityManager);
    }

    public static function getImageByTest(DoctrineFindParams $doctrineFindParams): ?Image
    {    
        return static::getContainer()->get(
            'doctrine')->getManager()->getRepository(
                Image::class)->findOneBy($doctrineFindParams->toArrayParams());
    }
}

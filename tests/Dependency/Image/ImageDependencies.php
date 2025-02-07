<?php

namespace Tests\Dependency\Image;

use App\Entity\Image;
use App\Repository\ImageRepository;
use App\Service\Image\ImageService;
use App\Service\Mapper\MapperCreateService;
use App\Service\Util\ResultOperationService;
use App\Util\DoctrineFindParams;
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

        $mapperServiceCreate = $container->get(MapperCreateService::class);
        $resultOperation = $container->get(ResultOperationService::class);


        return new ImageService(
            $container->get(ManagerRegistry::class),
            $container->get(MapperCreateService::class),
            $container->get(ResultOperationService::class)
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
            'doctrine'
        )->getManager()->getRepository(
            Image::class
        )->findOneBy($doctrineFindParams->toArrayParams());
    }
}

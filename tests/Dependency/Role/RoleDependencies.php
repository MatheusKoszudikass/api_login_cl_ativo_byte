<?php 

namespace Tests\Dependency\Role;

use App\Entity\Role;
use App\Repository\RoleRepository;
use App\Service\Mapper\MapperCreateService;
use App\Service\Mapper\MapperResponseService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Dependency\DatabaseTestCase;
use Doctrine\Persistence\ManagerRegistry;

class RoleDependencies extends KernelTestCase
{
    public function roleRepository(): RoleRepository
    {
        $database = new DatabaseTestCase();
        $database->setUp();
        $container = static::getContainer();
        
        $mapperServiceCreate = $container->get(MapperCreateService::class);
        $mapperServiceResponse = $container->get(MapperResponseService::class);
        $entityManager = $container->get(ManagerRegistry::class);
        
        return new RoleRepository(
            $entityManager,
            $mapperServiceCreate,
            $mapperServiceResponse
        );
    }

    public static function findRoleByName(string $name): Role
    {
        return static::getContainer()->get(
            'doctrine')->getManager()->getRepository(
                Role::class)->findOneBy(['name' => $name]);
    }
}
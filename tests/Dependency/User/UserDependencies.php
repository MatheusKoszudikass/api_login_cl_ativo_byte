<?php

namespace Tests\Dependency\User;

use App\Entity\User;
use App\Repository\RoleRepository;
use App\Service\MapperServiceCreate;
use App\Service\MapperServiceResponse;
use App\Service\EmailService;
use App\Service\TwoFactorAuthService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Repository\UserRepository;
use Tests\Dependency\DatabaseTestCase;

class UserDependencies extends KernelTestCase
{

    public function userRepository(): UserRepository
    {
        $database = new DatabaseTestCase();
        $database->setUp();
        $container = static::getContainer();
        
        $roleRepository = $container->get(RoleRepository::class);
        $mapperServiceCreate = $container->get(MapperServiceCreate::class);
        $mapperServiceResponse = $container->get(MapperServiceResponse::class);
        $mailer = $container->get(EmailService::class);
        $twoFactorAuthService = $container->get(TwoFactorAuthService::class);
        $entityManager = $container->get(ManagerRegistry::class);

        return new UserRepository(
            $entityManager, 
            $roleRepository,
            $mapperServiceCreate,
            $mapperServiceResponse,
            $mailer,
            $twoFactorAuthService
        ); 
    }

    public static function findUserByEmail(string $email): ?User
    {    
        return static::getContainer()->get(
            'doctrine')->getManager()->getRepository(
                User::class)->findOneBy(['email' => $email]);
    }
}
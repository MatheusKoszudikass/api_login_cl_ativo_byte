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
use App\Tests\Dependency\DatabaseTestCase;

class UserDependencies extends KernelTestCase
{
    private $container;

    public function userRepository(): UserRepository
    {
        $database = new DatabaseTestCase();
        $database->setUp();
        $this->container = static::getContainer();
        
        $roleRepository = $this->container->get(RoleRepository::class);
        $mapperServiceCreate = $this->container->get(MapperServiceCreate::class);
        $mapperServiceResponse = $this->container->get(MapperServiceResponse::class);
        $mailer = $this->container->get(EmailService::class);
        $twoFactorAuthService = $this->container->get(TwoFactorAuthService::class);
        $entityManager = $this->container->get(ManagerRegistry::class);

        
         $result = $twoFactorAuthService->generateToken();

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
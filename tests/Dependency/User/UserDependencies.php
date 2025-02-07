<?php

namespace Tests\Dependency\User;

use App\Entity\User;
use App\Service\Email\EmailService;
use App\Service\Mapper\MapperCreateService;
use App\Service\Mapper\MapperResponseService;
use App\Service\Util\ResultOperationService;
use App\Service\Auth\TwoFactorAuthService;
use App\Service\User\UserService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Dependency\DatabaseTestCase;

class UserDependencies extends KernelTestCase
{

    public function userService(): UserService
    {
        $database = new DatabaseTestCase();
        $database->setUp();
        $container = static::getContainer();

        return new UserService(
            $container->get(ManagerRegistry::class),
            $container->get(MapperCreateService::class),
            $container->get(MapperResponseService::class),
            $container->get(ResultOperationService::class),
            $container->get(TwoFactorAuthService::class),
            $container->get(EmailService::class)
        ); 
    }

    public static function findUserByEmail(string $email): ?User
    {    
        return static::getContainer()->get(
            'doctrine')->getManager()->getRepository(
                User::class)->findOneBy(['email' => $email]);
    }
}
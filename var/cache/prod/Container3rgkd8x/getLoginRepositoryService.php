<?php

namespace Container3rgkd8x;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/*
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getLoginRepositoryService extends App_KernelProdContainer
{
    /*
     * Gets the private 'App\Repository\LoginRepository' shared autowired service.
     *
     * @return \App\Repository\LoginRepository
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/doctrine/persistence/src/Persistence/ObjectRepository.php';
        include_once \dirname(__DIR__, 4).'/vendor/doctrine/collections/src/Selectable.php';
        include_once \dirname(__DIR__, 4).'/vendor/doctrine/orm/src/EntityRepository.php';
        include_once \dirname(__DIR__, 4).'/vendor/doctrine/doctrine-bundle/src/Repository/ServiceEntityRepositoryInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/doctrine/doctrine-bundle/src/Repository/ServiceEntityRepositoryProxy.php';
        include_once \dirname(__DIR__, 4).'/vendor/doctrine/doctrine-bundle/src/Repository/ServiceEntityRepository.php';
        include_once \dirname(__DIR__, 4).'/src/Interface/LoginRepositoryInterface.php';
        include_once \dirname(__DIR__, 4).'/src/Repository/LoginRepository.php';
        include_once \dirname(__DIR__, 4).'/src/Service/MapperService.php';
        include_once \dirname(__DIR__, 4).'/src/Service/EmailService.php';
        include_once \dirname(__DIR__, 4).'/src/Service/MapperServiceResponse.php';

        return $container->privates['App\\Repository\\LoginRepository'] = new \App\Repository\LoginRepository(($container->services['doctrine'] ?? self::getDoctrineService($container)), new \App\Service\MapperService(), ($container->privates['App\\Service\\EmailService'] ??= new \App\Service\EmailService()), ($container->privates['App\\Service\\TwoFactorAuthService'] ?? $container->load('getTwoFactorAuthServiceService')), ($container->privates['App\\Repository\\UserRepository'] ?? $container->load('getUserRepositoryService')), ($container->privates['App\\Service\\MapperServiceResponse'] ??= new \App\Service\MapperServiceResponse()));
    }
}

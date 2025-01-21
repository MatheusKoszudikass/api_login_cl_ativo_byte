<?php

namespace ContainerIjHn0RM;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getRoleRepositoryService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'App\Repository\RoleRepository' shared autowired service.
     *
     * @return \App\Repository\RoleRepository
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/doctrine/persistence/src/Persistence/ObjectRepository.php';
        include_once \dirname(__DIR__, 4).'/vendor/doctrine/collections/src/Selectable.php';
        include_once \dirname(__DIR__, 4).'/vendor/doctrine/orm/src/EntityRepository.php';
        include_once \dirname(__DIR__, 4).'/vendor/doctrine/doctrine-bundle/src/Repository/ServiceEntityRepositoryInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/doctrine/doctrine-bundle/src/Repository/ServiceEntityRepositoryProxy.php';
        include_once \dirname(__DIR__, 4).'/vendor/doctrine/doctrine-bundle/src/Repository/ServiceEntityRepository.php';
        include_once \dirname(__DIR__, 4).'/src/Interface/RoleRepositoryInterface.php';
        include_once \dirname(__DIR__, 4).'/src/Repository/RoleRepository.php';
        include_once \dirname(__DIR__, 4).'/src/Service/MapperServiceCreate.php';
        include_once \dirname(__DIR__, 4).'/src/Service/MapperServiceResponse.php';

        return $container->privates['App\\Repository\\RoleRepository'] = new \App\Repository\RoleRepository(($container->services['doctrine'] ?? self::getDoctrineService($container)), ($container->privates['App\\Service\\MapperServiceCreate'] ??= new \App\Service\MapperServiceCreate()), ($container->privates['App\\Service\\MapperServiceResponse'] ??= new \App\Service\MapperServiceResponse()));
    }
}

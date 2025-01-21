<?php

namespace ContainerIjHn0RM;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getRoleControllerService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'App\Controller\RoleController' shared autowired service.
     *
     * @return \App\Controller\RoleController
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/framework-bundle/Controller/AbstractController.php';
        include_once \dirname(__DIR__, 4).'/src/Controller/RoleController.php';

        $container->services['App\\Controller\\RoleController'] = $instance = new \App\Controller\RoleController(($container->privates['App\\Repository\\RoleRepository'] ?? $container->load('getRoleRepositoryService')));

        $instance->setContainer(($container->privates['.service_locator.QaaoWjx'] ?? $container->load('get_ServiceLocator_QaaoWjxService'))->withContext('App\\Controller\\RoleController', $container));

        return $instance;
    }
}

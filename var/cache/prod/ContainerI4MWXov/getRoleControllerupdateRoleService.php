<?php

namespace ContainerI4MWXov;


use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/*
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getRoleControllerupdateRoleService extends App_KernelProdContainer
{
    /*
     * Gets the private '.service_locator.bIZOrlv.App\Controller\RoleController::updateRole()' shared service.
     *
     * @return \Symfony\Component\DependencyInjection\ServiceLocator
     */
    public static function do($container, $lazyLoad = true)
    {
        return $container->privates['.service_locator.bIZOrlv.App\\Controller\\RoleController::updateRole()'] = ($container->privates['.service_locator.bIZOrlv'] ?? $container->load('get_ServiceLocator_BIZOrlvService'))->withContext('App\\Controller\\RoleController::updateRole()', $container);
    }
}

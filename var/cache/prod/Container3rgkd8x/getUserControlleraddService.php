<?php

namespace Container3rgkd8x;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/*
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getUserControlleraddService extends App_KernelProdContainer
{
    /*
     * Gets the private '.service_locator.R7hZWdi.App\Controller\UserController::add()' shared service.
     *
     * @return \Symfony\Component\DependencyInjection\ServiceLocator
     */
    public static function do($container, $lazyLoad = true)
    {
        return $container->privates['.service_locator.R7hZWdi.App\\Controller\\UserController::add()'] = ($container->privates['.service_locator.R7hZWdi'] ?? $container->load('get_ServiceLocator_R7hZWdiService'))->withContext('App\\Controller\\UserController::add()', $container);
    }
}

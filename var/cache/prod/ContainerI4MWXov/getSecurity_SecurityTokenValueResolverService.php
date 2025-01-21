<?php

namespace ContainerI4MWXov;


use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/*
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getSecurity_SecurityTokenValueResolverService extends App_KernelProdContainer
{
    /*
     * Gets the private 'security.security_token_value_resolver' shared service.
     *
     * @return \Symfony\Component\Security\Http\Controller\SecurityTokenValueResolver
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).''.\DIRECTORY_SEPARATOR.'vendor'.\DIRECTORY_SEPARATOR.'symfony'.\DIRECTORY_SEPARATOR.'http-kernel'.\DIRECTORY_SEPARATOR.'Controller'.\DIRECTORY_SEPARATOR.'ValueResolverInterface.php';
        include_once \dirname(__DIR__, 4).''.\DIRECTORY_SEPARATOR.'vendor'.\DIRECTORY_SEPARATOR.'symfony'.\DIRECTORY_SEPARATOR.'security-http'.\DIRECTORY_SEPARATOR.'Controller'.\DIRECTORY_SEPARATOR.'SecurityTokenValueResolver.php';

        return $container->privates['security.security_token_value_resolver'] = new \Symfony\Component\Security\Http\Controller\SecurityTokenValueResolver(($container->privates['security.token_storage'] ?? self::getSecurity_TokenStorageService($container)));
    }
}

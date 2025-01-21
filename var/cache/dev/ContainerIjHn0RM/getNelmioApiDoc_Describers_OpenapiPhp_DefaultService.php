<?php

namespace ContainerIjHn0RM;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getNelmioApiDoc_Describers_OpenapiPhp_DefaultService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'nelmio_api_doc.describers.openapi_php.default' shared service.
     *
     * @return \Nelmio\ApiDocBundle\Describer\OpenApiPhpDescriber
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/nelmio/api-doc-bundle/src/Util/SetsContextTrait.php';
        include_once \dirname(__DIR__, 4).'/vendor/nelmio/api-doc-bundle/src/Describer/OpenApiPhpDescriber.php';
        include_once \dirname(__DIR__, 4).'/vendor/nelmio/api-doc-bundle/src/Util/ControllerReflector.php';

        return $container->privates['nelmio_api_doc.describers.openapi_php.default'] = new \Nelmio\ApiDocBundle\Describer\OpenApiPhpDescriber(($container->privates['nelmio_api_doc.routes.default'] ?? $container->load('getNelmioApiDoc_Routes_DefaultService')), ($container->privates['nelmio_api_doc.controller_reflector'] ??= new \Nelmio\ApiDocBundle\Util\ControllerReflector($container)), NULL, ($container->privates['logger'] ?? self::getLoggerService($container)));
    }
}

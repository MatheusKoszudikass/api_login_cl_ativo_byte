<?php

namespace ContainerIjHn0RM;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getNelmioApiDoc_Describers_Route_DefaultService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'nelmio_api_doc.describers.route.default' shared service.
     *
     * @return \Nelmio\ApiDocBundle\Describer\RouteDescriber
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/nelmio/api-doc-bundle/src/Describer/DescriberInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/nelmio/api-doc-bundle/src/Describer/ModelRegistryAwareInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/nelmio/api-doc-bundle/src/Describer/ModelRegistryAwareTrait.php';
        include_once \dirname(__DIR__, 4).'/vendor/nelmio/api-doc-bundle/src/Describer/RouteDescriber.php';
        include_once \dirname(__DIR__, 4).'/vendor/nelmio/api-doc-bundle/src/Util/ControllerReflector.php';

        return $container->privates['nelmio_api_doc.describers.route.default'] = new \Nelmio\ApiDocBundle\Describer\RouteDescriber(($container->privates['nelmio_api_doc.routes.default'] ?? $container->load('getNelmioApiDoc_Routes_DefaultService')), ($container->privates['nelmio_api_doc.controller_reflector'] ??= new \Nelmio\ApiDocBundle\Util\ControllerReflector($container)), new RewindableGenerator(function () use ($container) {
            yield 0 => ($container->privates['nelmio_api_doc.route_describers.route_argument'] ?? $container->load('getNelmioApiDoc_RouteDescribers_RouteArgumentService'));
            yield 1 => ($container->privates['nelmio_api_doc.route_describers.php_doc'] ??= new \Nelmio\ApiDocBundle\RouteDescriber\PhpDocDescriber());
            yield 2 => ($container->privates['nelmio_api_doc.route_describers.route_metadata'] ??= new \Nelmio\ApiDocBundle\RouteDescriber\RouteMetadataDescriber());
        }, 3));
    }
}

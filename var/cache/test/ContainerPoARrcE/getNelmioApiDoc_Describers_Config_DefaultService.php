<?php

namespace ContainerPoARrcE;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getNelmioApiDoc_Describers_Config_DefaultService extends App_KernelTestDebugContainer
{
    /**
     * Gets the private 'nelmio_api_doc.describers.config.default' shared service.
     *
     * @return \Nelmio\ApiDocBundle\Describer\ExternalDocDescriber
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/nelmio/api-doc-bundle/src/Describer/DescriberInterface.php';
        include_once \dirname(__DIR__, 4).'/vendor/nelmio/api-doc-bundle/src/Describer/ExternalDocDescriber.php';

        return $container->privates['nelmio_api_doc.describers.config.default'] = new \Nelmio\ApiDocBundle\Describer\ExternalDocDescriber([], true);
    }
}

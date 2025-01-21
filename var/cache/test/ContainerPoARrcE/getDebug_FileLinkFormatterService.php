<?php

namespace ContainerPoARrcE;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getDebug_FileLinkFormatterService extends App_KernelTestDebugContainer
{
    /**
     * Gets the private 'debug.file_link_formatter' shared service.
     *
     * @return \Symfony\Component\ErrorHandler\ErrorRenderer\FileLinkFormatter
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/error-handler/ErrorRenderer/FileLinkFormatter.php';

        return $container->privates['debug.file_link_formatter'] = new \Symfony\Component\ErrorHandler\ErrorRenderer\FileLinkFormatter($container->getEnv('default::SYMFONY_IDE'));
    }
}

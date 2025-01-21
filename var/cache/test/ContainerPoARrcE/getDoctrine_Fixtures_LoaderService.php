<?php

namespace ContainerPoARrcE;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getDoctrine_Fixtures_LoaderService extends App_KernelTestDebugContainer
{
    /**
     * Gets the private 'doctrine.fixtures.loader' shared service.
     *
     * @return \Doctrine\Bundle\FixturesBundle\Loader\SymfonyFixturesLoader
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/doctrine/data-fixtures/src/Loader.php';
        include_once \dirname(__DIR__, 4).'/vendor/doctrine/doctrine-fixtures-bundle/src/Loader/SymfonyBridgeLoader.php';
        include_once \dirname(__DIR__, 4).'/vendor/doctrine/doctrine-fixtures-bundle/src/Loader/SymfonyFixturesLoader.php';

        $container->privates['doctrine.fixtures.loader'] = $instance = new \Doctrine\Bundle\FixturesBundle\Loader\SymfonyFixturesLoader($container);

        $instance->addFixtures([]);

        return $instance;
    }
}

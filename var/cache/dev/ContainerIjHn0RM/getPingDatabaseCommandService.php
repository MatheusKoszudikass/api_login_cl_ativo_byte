<?php

namespace ContainerIjHn0RM;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getPingDatabaseCommandService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private 'App\Command\PingDatabaseCommand' shared autowired service.
     *
     * @return \App\Command\PingDatabaseCommand
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/console/Command/Command.php';
        include_once \dirname(__DIR__, 4).'/src/Command/PingDatabaseCommand.php';

        $container->privates['App\\Command\\PingDatabaseCommand'] = $instance = new \App\Command\PingDatabaseCommand();

        $instance->setName('app:ping-database');
        $instance->setDescription('Add a short description for your command');

        return $instance;
    }
}

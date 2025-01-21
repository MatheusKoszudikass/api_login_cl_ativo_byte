<?php

namespace ContainerPoARrcE;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getCache_SecurityIsCsrfTokenValidAttributeExpressionLanguageService extends App_KernelTestDebugContainer
{
    /**
     * Gets the public 'cache.security_is_csrf_token_valid_attribute_expression_language' shared service.
     *
     * @return \Symfony\Component\Cache\Adapter\AdapterInterface
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/cache/Traits/AbstractAdapterTrait.php';
        include_once \dirname(__DIR__, 4).'/vendor/symfony/cache-contracts/CacheTrait.php';
        include_once \dirname(__DIR__, 4).'/vendor/symfony/cache/Traits/ContractsTrait.php';
        include_once \dirname(__DIR__, 4).'/vendor/symfony/cache/Adapter/AbstractAdapter.php';

        return $container->services['cache.security_is_csrf_token_valid_attribute_expression_language'] = \Symfony\Component\Cache\Adapter\AbstractAdapter::createSystemCache('jFXbrtt8FK', 0, $container->getParameter('container.build_id'), ($container->targetDir.''.'/pools/system'), ($container->privates['logger'] ?? self::getLoggerService($container)));
    }
}

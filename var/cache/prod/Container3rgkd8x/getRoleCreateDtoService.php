<?php

namespace Container3rgkd8x;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/*
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getRoleCreateDtoService extends App_KernelProdContainer
{
    /*
     * Gets the private 'App\Dto\Create\RoleCreateDto' shared autowired service.
     *
     * @return \App\Dto\Create\RoleCreateDto
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/src/Dto/BaseEntityDto.php';
        include_once \dirname(__DIR__, 4).'/src/Dto/Create/RoleCreateDto.php';

        return $container->privates['App\\Dto\\Create\\RoleCreateDto'] = new \App\Dto\Create\RoleCreateDto();
    }
}

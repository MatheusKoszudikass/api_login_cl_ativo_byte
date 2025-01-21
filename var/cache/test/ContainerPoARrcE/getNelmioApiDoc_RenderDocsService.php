<?php

namespace ContainerPoARrcE;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getNelmioApiDoc_RenderDocsService extends App_KernelTestDebugContainer
{
    /**
     * Gets the public 'nelmio_api_doc.render_docs' shared service.
     *
     * @return \Nelmio\ApiDocBundle\Render\RenderOpenApi
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/nelmio/api-doc-bundle/src/Render/RenderOpenApi.php';
        include_once \dirname(__DIR__, 4).'/vendor/nelmio/api-doc-bundle/src/Render/OpenApiRenderer.php';
        include_once \dirname(__DIR__, 4).'/vendor/nelmio/api-doc-bundle/src/Render/Json/JsonOpenApiRenderer.php';
        include_once \dirname(__DIR__, 4).'/vendor/nelmio/api-doc-bundle/src/Render/Yaml/YamlOpenApiRenderer.php';

        return $container->services['nelmio_api_doc.render_docs'] = new \Nelmio\ApiDocBundle\Render\RenderOpenApi(($container->privates['nelmio_api_doc.generator_locator'] ?? $container->load('getNelmioApiDoc_GeneratorLocatorService')), ($container->privates['nelmio_api_doc.render_docs.json'] ??= new \Nelmio\ApiDocBundle\Render\Json\JsonOpenApiRenderer()), ($container->privates['nelmio_api_doc.render_docs.yaml'] ??= new \Nelmio\ApiDocBundle\Render\Yaml\YamlOpenApiRenderer()), ($container->privates['nelmio_api_doc.render_docs.html'] ?? $container->load('getNelmioApiDoc_RenderDocs_HtmlService')));
    }
}

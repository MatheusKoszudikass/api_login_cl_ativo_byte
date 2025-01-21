<?php

namespace Symfony\Config\NelmioApiDoc;

require_once __DIR__.\DIRECTORY_SEPARATOR.'AreasConfig'.\DIRECTORY_SEPARATOR.'CacheConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class AreasConfig 
{
    private $pathPatterns;
    private $hostPatterns;
    private $namePatterns;
    private $withAnnotation;
    private $withAttribute;
    private $disableDefaultRoutes;
    private $documentation;
    private $cache;
    private $_usedProperties = [];

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function pathPatterns(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['pathPatterns'] = true;
        $this->pathPatterns = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function hostPatterns(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['hostPatterns'] = true;
        $this->hostPatterns = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     */
    public function namePatterns(ParamConfigurator|array $value): static
    {
        $this->_usedProperties['namePatterns'] = true;
        $this->namePatterns = $value;

        return $this;
    }

    /**
     * whether to filter by annotation
     * @default false
     * @param ParamConfigurator|bool $value
     * @deprecated The "with_annotation" option is deprecated. Use "with_attribute" instead.
     * @return $this
     */
    public function withAnnotation($value): static
    {
        $this->_usedProperties['withAnnotation'] = true;
        $this->withAnnotation = $value;

        return $this;
    }

    /**
     * whether to filter by attribute
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function withAttribute($value): static
    {
        $this->_usedProperties['withAttribute'] = true;
        $this->withAttribute = $value;

        return $this;
    }

    /**
     * if set disables default routes without annotations
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function disableDefaultRoutes($value): static
    {
        $this->_usedProperties['disableDefaultRoutes'] = true;
        $this->disableDefaultRoutes = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function documentation(string $key, mixed $value): static
    {
        $this->_usedProperties['documentation'] = true;
        $this->documentation[$key] = $value;

        return $this;
    }

    public function cache(array $value = []): \Symfony\Config\NelmioApiDoc\AreasConfig\CacheConfig
    {
        if (null === $this->cache) {
            $this->_usedProperties['cache'] = true;
            $this->cache = new \Symfony\Config\NelmioApiDoc\AreasConfig\CacheConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "cache()" has already been initialized. You cannot pass values the second time you call cache().');
        }

        return $this->cache;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('path_patterns', $value)) {
            $this->_usedProperties['pathPatterns'] = true;
            $this->pathPatterns = $value['path_patterns'];
            unset($value['path_patterns']);
        }

        if (array_key_exists('host_patterns', $value)) {
            $this->_usedProperties['hostPatterns'] = true;
            $this->hostPatterns = $value['host_patterns'];
            unset($value['host_patterns']);
        }

        if (array_key_exists('name_patterns', $value)) {
            $this->_usedProperties['namePatterns'] = true;
            $this->namePatterns = $value['name_patterns'];
            unset($value['name_patterns']);
        }

        if (array_key_exists('with_annotation', $value)) {
            $this->_usedProperties['withAnnotation'] = true;
            $this->withAnnotation = $value['with_annotation'];
            unset($value['with_annotation']);
        }

        if (array_key_exists('with_attribute', $value)) {
            $this->_usedProperties['withAttribute'] = true;
            $this->withAttribute = $value['with_attribute'];
            unset($value['with_attribute']);
        }

        if (array_key_exists('disable_default_routes', $value)) {
            $this->_usedProperties['disableDefaultRoutes'] = true;
            $this->disableDefaultRoutes = $value['disable_default_routes'];
            unset($value['disable_default_routes']);
        }

        if (array_key_exists('documentation', $value)) {
            $this->_usedProperties['documentation'] = true;
            $this->documentation = $value['documentation'];
            unset($value['documentation']);
        }

        if (array_key_exists('cache', $value)) {
            $this->_usedProperties['cache'] = true;
            $this->cache = new \Symfony\Config\NelmioApiDoc\AreasConfig\CacheConfig($value['cache']);
            unset($value['cache']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['pathPatterns'])) {
            $output['path_patterns'] = $this->pathPatterns;
        }
        if (isset($this->_usedProperties['hostPatterns'])) {
            $output['host_patterns'] = $this->hostPatterns;
        }
        if (isset($this->_usedProperties['namePatterns'])) {
            $output['name_patterns'] = $this->namePatterns;
        }
        if (isset($this->_usedProperties['withAnnotation'])) {
            $output['with_annotation'] = $this->withAnnotation;
        }
        if (isset($this->_usedProperties['withAttribute'])) {
            $output['with_attribute'] = $this->withAttribute;
        }
        if (isset($this->_usedProperties['disableDefaultRoutes'])) {
            $output['disable_default_routes'] = $this->disableDefaultRoutes;
        }
        if (isset($this->_usedProperties['documentation'])) {
            $output['documentation'] = $this->documentation;
        }
        if (isset($this->_usedProperties['cache'])) {
            $output['cache'] = $this->cache->toArray();
        }

        return $output;
    }

}

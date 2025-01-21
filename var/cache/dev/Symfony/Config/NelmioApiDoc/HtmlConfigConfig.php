<?php

namespace Symfony\Config\NelmioApiDoc;

require_once __DIR__.\DIRECTORY_SEPARATOR.'HtmlConfig'.\DIRECTORY_SEPARATOR.'SwaggerUiConfigConfig.php';
require_once __DIR__.\DIRECTORY_SEPARATOR.'HtmlConfig'.\DIRECTORY_SEPARATOR.'RedoclyConfigConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class HtmlConfigConfig 
{
    private $assetsMode;
    private $swaggerUiConfig;
    private $redoclyConfig;
    private $_usedProperties = [];

    /**
     * @default 'cdn'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function assetsMode($value): static
    {
        $this->_usedProperties['assetsMode'] = true;
        $this->assetsMode = $value;

        return $this;
    }

    /**
     * https://swagger.io/docs/open-source-tools/swagger-ui/usage/configuration/
    */
    public function swaggerUiConfig(array $value = []): \Symfony\Config\NelmioApiDoc\HtmlConfig\SwaggerUiConfigConfig
    {
        if (null === $this->swaggerUiConfig) {
            $this->_usedProperties['swaggerUiConfig'] = true;
            $this->swaggerUiConfig = new \Symfony\Config\NelmioApiDoc\HtmlConfig\SwaggerUiConfigConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "swaggerUiConfig()" has already been initialized. You cannot pass values the second time you call swaggerUiConfig().');
        }

        return $this->swaggerUiConfig;
    }

    /**
     * https://redocly.com/docs/redoc/config/
    */
    public function redoclyConfig(array $value = []): \Symfony\Config\NelmioApiDoc\HtmlConfig\RedoclyConfigConfig
    {
        if (null === $this->redoclyConfig) {
            $this->_usedProperties['redoclyConfig'] = true;
            $this->redoclyConfig = new \Symfony\Config\NelmioApiDoc\HtmlConfig\RedoclyConfigConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "redoclyConfig()" has already been initialized. You cannot pass values the second time you call redoclyConfig().');
        }

        return $this->redoclyConfig;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('assets_mode', $value)) {
            $this->_usedProperties['assetsMode'] = true;
            $this->assetsMode = $value['assets_mode'];
            unset($value['assets_mode']);
        }

        if (array_key_exists('swagger_ui_config', $value)) {
            $this->_usedProperties['swaggerUiConfig'] = true;
            $this->swaggerUiConfig = new \Symfony\Config\NelmioApiDoc\HtmlConfig\SwaggerUiConfigConfig($value['swagger_ui_config']);
            unset($value['swagger_ui_config']);
        }

        if (array_key_exists('redocly_config', $value)) {
            $this->_usedProperties['redoclyConfig'] = true;
            $this->redoclyConfig = new \Symfony\Config\NelmioApiDoc\HtmlConfig\RedoclyConfigConfig($value['redocly_config']);
            unset($value['redocly_config']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['assetsMode'])) {
            $output['assets_mode'] = $this->assetsMode;
        }
        if (isset($this->_usedProperties['swaggerUiConfig'])) {
            $output['swagger_ui_config'] = $this->swaggerUiConfig->toArray();
        }
        if (isset($this->_usedProperties['redoclyConfig'])) {
            $output['redocly_config'] = $this->redoclyConfig->toArray();
        }

        return $output;
    }

}

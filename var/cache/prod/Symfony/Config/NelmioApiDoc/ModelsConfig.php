<?php

namespace Symfony\Config\NelmioApiDoc;

require_once __DIR__.\DIRECTORY_SEPARATOR.'Models'.\DIRECTORY_SEPARATOR.'NamesConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class ModelsConfig 
{
    private $useJms;
    private $names;
    private $_usedProperties = [];

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function useJms($value): static
    {
        $this->_usedProperties['useJms'] = true;
        $this->useJms = $value;

        return $this;
    }

    public function names(array $value = []): \Symfony\Config\NelmioApiDoc\Models\NamesConfig
    {
        $this->_usedProperties['names'] = true;

        return $this->names[] = new \Symfony\Config\NelmioApiDoc\Models\NamesConfig($value);
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('use_jms', $value)) {
            $this->_usedProperties['useJms'] = true;
            $this->useJms = $value['use_jms'];
            unset($value['use_jms']);
        }

        if (array_key_exists('names', $value)) {
            $this->_usedProperties['names'] = true;
            $this->names = array_map(fn ($v) => new \Symfony\Config\NelmioApiDoc\Models\NamesConfig($v), $value['names']);
            unset($value['names']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['useJms'])) {
            $output['use_jms'] = $this->useJms;
        }
        if (isset($this->_usedProperties['names'])) {
            $output['names'] = array_map(fn ($v) => $v->toArray(), $this->names);
        }

        return $output;
    }

}

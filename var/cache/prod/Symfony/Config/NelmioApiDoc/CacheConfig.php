<?php

namespace Symfony\Config\NelmioApiDoc;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class CacheConfig 
{
    private $pool;
    private $itemId;
    private $_usedProperties = [];

    /**
     * define cache pool to use
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function pool($value): static
    {
        $this->_usedProperties['pool'] = true;
        $this->pool = $value;

        return $this;
    }

    /**
     * define cache item id
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function itemId($value): static
    {
        $this->_usedProperties['itemId'] = true;
        $this->itemId = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('pool', $value)) {
            $this->_usedProperties['pool'] = true;
            $this->pool = $value['pool'];
            unset($value['pool']);
        }

        if (array_key_exists('item_id', $value)) {
            $this->_usedProperties['itemId'] = true;
            $this->itemId = $value['item_id'];
            unset($value['item_id']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['pool'])) {
            $output['pool'] = $this->pool;
        }
        if (isset($this->_usedProperties['itemId'])) {
            $output['item_id'] = $this->itemId;
        }

        return $output;
    }

}

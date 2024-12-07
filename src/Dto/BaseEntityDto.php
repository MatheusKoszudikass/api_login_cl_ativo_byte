<?php

namespace App\Dto;

abstract class BaseEntityDto
{
    public function isEmpty(): bool
    {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            if (!empty($property->getValue($this))) {
                return false;
            }
        }

        return true;
    }
}
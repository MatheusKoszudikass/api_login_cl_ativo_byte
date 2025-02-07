<?php

namespace App\Dto;

abstract class BaseEntityDto
{
    public function isEmpty(): bool
    {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            $value = $property->getValue($this);

            if(is_null($value) || $value == "")  return false;
        }

        return true;
    }
}
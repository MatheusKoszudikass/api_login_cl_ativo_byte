<?php

namespace App\Util;

class PayLoadJwt
{
    private string $property = '';
    private string $value = '';
    private string|array $payload = [];

    public function getProperty(): string
    {
        return $this->property;
    }

    public function setProperty(string $property): void
    {
        $this->property = $property;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getPayload(): string|array
    {
        return $this->payload;
    }

    public function setPayload(string|array $payload): void
    {
        if(is_array($payload))
        {
            $this->payload = [];
            return;
        }
        
        $this->payload = $payload;
    }

    public function addProperty(string $property, string $value): void
    {
        if(is_string($this->payload)) {
            $this->payload = [];
        }

        $this->payload[$property] = $value;
    }
}

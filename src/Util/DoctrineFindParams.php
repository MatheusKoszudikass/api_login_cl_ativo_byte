<?php

namespace App\Util;

use App\Entity\Enum\TypeEntitiesEnum;

class DoctrineFindParams
{
    public function __construct(
        private readonly string $property,
        private readonly string|array $identifier,
        private readonly TypeEntitiesEnum $type
    ) {}

    public function toArrayParams (): array
    {
        return  [$this->getProperty() => $this->getIdentifier()];
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function getIdentifier(): string|array
    {
        return $this->identifier;
    }

    public function isEmptyDoctrineFindParams(): bool
    {
        return empty($this->property) || empty($this->identifier);
    }

    public function getType(): TypeEntitiesEnum
    {
        return $this->type;
    }
}

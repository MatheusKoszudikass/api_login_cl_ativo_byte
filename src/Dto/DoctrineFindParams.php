<?php

namespace App\Dto;

use App\Entity\Enum\TypeImageEnum;
use Doctrine\DBAL\Types\Type;

class DoctrineFindParams
{
    public function __construct(
        private readonly string $property,
        private readonly string $identifier,
        private readonly TypeImageEnum $class
    ) {}

    public function toArrayParams (): array
    {
        return  [$this->getProperty() => $this->getIdentifier()];
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getClass(): TypeImageEnum
    {
        return $this->class;
    }
}

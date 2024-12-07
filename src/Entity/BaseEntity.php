<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;

abstract class BaseEntity
{
    #[ORM\Id]
    #[ORM\Column(type: Types::GUID)]
    protected string $id;
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected DateTime $dateCreated;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected DateTime $dateUpdated;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected DateTime $systemAccess;

    public function __construct()
    {
        $this->id = $this->generateUuid();
        $this->dateCreated = new DateTime('now');
        $this->dateUpdated = new DateTime('now');
        $this->systemAccess = new DateTime('now');
    }
    
    /**
     * Generates a version 4 UUID according to RFC 4122
     *
     * @return string
     */
    private function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * Sets the system access date to the current date and time
     */
    public function setSystemAccess() : void 
    {
      $this->systemAccess = new DateTime('now');
    }

    /**
     * Checks if the object is empty, i.e. if all its public properties are empty
     *
     * @return bool
     */
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
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

        if (!is_executable("../dependencies/bin/UUID_GENERATOR")) {
            shell_exec("chmod +x ../dependencies/bin/UUID_GENERATOR");
        }
    }

    /**
     * Executes a binary that makes an UUID, checks it and, at all, return it
     */
    private function generateUuid(): string
    {
       $output = shell_exec("./../dependencies/bin/UUID_GENERATOR 2>&1");
        if ($output === null) {
            // Checks if that worked
            echo "Failed to make a UUID";
        } else {
            return trim($output);
        }
    }

    public function getId(): string 
    {
        return $this->id;
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

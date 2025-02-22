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

    // Path to the UUID_GENERATOR binary
    private string $uuidGeneratorPath;

    public function __construct()
    {
        // Initialize UUID generator path
        $this->uuidGeneratorPath = __DIR__ . "/../dependencies/bin/UUID_GENERATOR";

        $this->id = $this->generateUuid();
        $this->dateCreated = new DateTime('now');
        $this->dateUpdated = new DateTime('now');
        $this->systemAccess = new DateTime('now');

        // Checks if the UUID_GENERATOR binary is executable
        if (!is_executable($this->uuidGeneratorPath)) {
            // Changes permissions only if necessary
            shell_exec("chmod +x $this->uuidGeneratorPath");
        }
    }

    /**
     * Executes the binary that generates a UUID, checks if it worked, and returns the UUID
     */
    private function generateUuid(): string
    {
        $output = shell_exec($this->uuidGeneratorPath);
        
        // Checks if the execution was successful
        if ($output === null || trim($output) === '') {
            // If it fails, throws an exception
            throw new \Exception("Failed to generate a UUID. Check if the UUID_GENERATOR binary is accessible.");
        }
        
        return trim($output);
    }

    public function getId(): string 
    {
        return $this->id;
    }

    /**
     * Sets the system access date to the current date and time
     */
    public function setSystemAccess(): void 
    {
        $this->systemAccess = new DateTime('now');
    }

    /**
     * Checks if the object is empty, i.e., if all its public properties are empty
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

<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use DateTime;
use DateTimeImmutable;

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


    // public function getDateCreated(): DateTimeImmutable
    // {
    //     return $this->dateCreated;
    // }


    // public function getDateUpdated(): DateTimeImmutable
    // {    
    //     return $this->dateUpdated;
    // }

    // public function getSystemAccess(): DateTimeImmutable
    // {
    //     return $this->systemAccess;
    // }

    // public function updateDateUpdated(): void
    // {
    //     $this->dateUpdated = new DateTimeImmutable('now');
    // }
}
<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\DBAL\Types\GuidType;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role extends BaseEntity
{
    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private string $description;

    /**
     *   @ORM\ManyToOne(targetEntity="User", inversedBy="roles" )
     */
    private  $users;

    public function __construct(string $roles)
    {
        parent::__construct();
        $this->name = $roles;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(string $description): string
    {
        return $this->description = $description;
    }
}

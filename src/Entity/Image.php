<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Enum\TypeImageEnum;
use App\Entity\Enum\TypeImageExtensionEnum;
use InvalidArgumentException;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image extends BaseEntity
{
    #[ORM\Column(length: 50)]
    private string $name;

    #[ORM\Column(length: 255)]
    private string $path;

    #[ORM\Column(length: 50)]
    private string $typeImage;

    #[ORM\Column(length: 4)]
    private string $typeImageExtension;

    #[ORM\Column(length: 50)]
    private string $ownerClass;

    #[ORM\Column(length: 50)]
    private string $ownerId;

    public function __construct(
        string $name,
        string $path,
        TypeImageEnum $typeImage,
        string $ownerClass,
        string $ownerId
    ) {
        parent::__construct();
        $this->name = $name;
        $this->path = $path;
        $this->typeImage = $typeImage->value;
        $this->typeImageExtension = $this->extractExtension($path);
        $this->ownerClass = $ownerClass;
        $this->ownerId = $ownerId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;
        $this->typeImageExtension = $this->extractExtension($path);
        return $this;
    }

    public function getTypeImage(): TypeImageEnum
    {
        return TypeImageEnum::from($this->typeImage);
    }

    public function setTypeImage(TypeImageEnum $typeImage): static
    {
        $this->typeImage = $typeImage->value;
        return $this;
    }

    public function getTypeImageExtension(): TypeImageExtensionEnum
    {
        return TypeImageExtensionEnum::from($this->typeImageExtension);
    }

    public function setTypeImageExtension(TypeImageExtensionEnum $typeImageExtension): static
    {
        $this->typeImageExtension = $typeImageExtension->value;
        return $this;
    }

    private function extractExtension(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    
        if (empty($extension) || !TypeImageExtensionEnum::tryFrom($extension)) {
            throw new InvalidArgumentException("A extensão '$extension' não é válida.");
        }
    
        return $extension;
    }

    public function getOwnerClass(): string
    {
        return $this->ownerClass;
    }

    public function setOwnerClass(string $ownerClass): static
    {
        $this->ownerClass = $ownerClass;
        return $this;
    }

    public function getOwnerId(): string
    {
        return $this->ownerId;
    }

    public function setOwnerId(string $ownerId): static
    {
        $this->ownerId = $ownerId;
        return $this;
    }
}

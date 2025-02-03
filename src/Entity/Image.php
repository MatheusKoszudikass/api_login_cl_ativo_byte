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
        $this->name = $this->validateNotEmpty($name, "O nome da imagem não pode ser vazio.");
        $this->path = $this->validateNotEmpty($path, "O campo 'path' não pode ser vazio.");
        $this->typeImage = $this->validateNotEmpty($typeImage->value, "O campo 'typeImage' não pode ser vazio.");
        $this->ownerClass = $this->validateNotEmpty($ownerClass, "O campo 'ownerClass' não pode ser vazio.");
        $this->ownerId = $this->validateNotEmpty($ownerId, "O campo 'ownerId' não pode ser vazio.");
        $this->typeImageExtension = $this->extractExtension($path);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $this->validateNotEmpty($name, "O nome da imagem não pode ser vazio.");
        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $this->validateNotEmpty($path, "O campo 'path' não pode ser vazio.");
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
        $this->typeImageExtension = $this->validateNotEmpty(
            $typeImageExtension->value, "O campo 'typeImageExtension' não pode ser vazio ou valor inválido.");

        return $this;
    }

    public function getOwnerClass(): string
    {
        return $this->ownerClass;
    }

    public function setOwnerClass(string $ownerClass): static
    {
        $this->ownerClass = $this->validateNotEmpty(
            $ownerClass, "O campo 'ownerClass' não pode ser vazio.");

        return $this;
    }

    public function getOwnerId(): string
    {
        return $this->ownerId;
    }

    public function setOwnerId(string $ownerId): static
    {
        $this->ownerId = $this->validateNotEmpty(
            $ownerId, "O campo 'ownerId' não pode ser vazio.");

        return $this;
    }

    private function extractExtension(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return TypeImageExtensionEnum::tryFrom($extension)
            ? $extension
            : throw new InvalidArgumentException("A extensão '$extension' não é válida.");
    }

    private function validateNotEmpty(string $value, string $errorMessage): string
    {
        if (empty($value)) {
            throw new InvalidArgumentException($errorMessage);
        }
        return $value;
    }
}

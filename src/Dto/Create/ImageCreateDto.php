<?php

namespace App\Dto\Create;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Enum\TypeImageEnum;
use App\Dto\BaseEntityDto;

class ImageCreateDto extends BaseEntityDto
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: 50, maxMessage: 'O nome da imagem deve ter no máximo 50 caracteres')]
    public string $name = '';

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: 255, maxMessage: 'O caminho da imagem deve ter no máximo 255 caracteres')]
    public string $path = '';

    #[Assert\NotBlank]
    #[Assert\Type(TypeImageEnum::class)]
    public TypeImageEnum $typeImage = TypeImageEnum::PRODUCT;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: 15, maxMessage: 'A classe do propriário da imagem deve ter no máximo 15 caracteres')]
    public string $ownerClass = '';

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[Assert\Length(max: 36, maxMessage: 'O ID do propriário da imagem deve ter no máximo 36 caracteres')]
    public string $ownerId = '';
}


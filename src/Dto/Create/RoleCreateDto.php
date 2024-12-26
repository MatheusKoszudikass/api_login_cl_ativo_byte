<?php

namespace App\Dto\Create;

use Symfony\Component\Validator\Constraints as Assert;
use App\Dto\BaseEntityDto;

class RoleCreateDto extends BaseEntityDto
{
    public string $id = '';

    #[Assert\NotBlank]
    public string $name = '';

    public string $description = '';
}
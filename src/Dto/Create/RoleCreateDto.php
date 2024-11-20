<?php

namespace App\Dto\Create;

use Symfony\Component\Validator\Constraints as Assert;

class RoleCreateDto
{
    public string $id;

    #[Assert\NotBlank]
    public string $name;

    public string $description;
}
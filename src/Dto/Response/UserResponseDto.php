<?php

namespace App\Dto\Response;

use App\Dto\BaseEntityDto;
use Doctrine\DBAL\Types\GuidType;

class UserResponseDto extends BaseEntityDto
{
    public string $id;

    public string $email;

    public ?string $firstName;

    public ?string $lastName;

    public ?string $cnpjCpfRg;

    public ?string $userName;

    public ?string $legalRegister;

    public array $roles = [];
}

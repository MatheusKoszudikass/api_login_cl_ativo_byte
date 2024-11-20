<?php

namespace App\Dto\Response;

use Doctrine\DBAL\Types\GuidType;

class UserResponseDto
{
    public string $id;

    public string $email;

    public ?string $firstName;

    public ?string $lastName;

    public ?string $cnpjCpfRg;

    public ?string $userName;

    public ?string $token;

    public array $roles = [];
}

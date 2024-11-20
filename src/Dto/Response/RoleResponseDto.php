<?php

namespace App\Dto\Response;

use Doctrine\DBAL\Types\Types;


class RoleResponseDto
{
    public string $id;
    public string $name;
    public string $description;
}
<?php

namespace App\Dto\Response;

use App\Dto\BaseEntityDto;


class RoleResponseDto extends BaseEntityDto
{
    public string $id;
    public string $name;
    public string $description;
}
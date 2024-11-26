<?php

namespace App\Service;

use App\Dto\Response\RoleResponseDto;
use App\Dto\Response\UserResponseDto;
use App\Entity\Role;
use App\Entity\User;


class MapperServiceResponse
{

    public function mapRoleToDto(Role $role): RoleResponseDto
    {
        $dto = new RoleResponseDto();
        $dto->id = $role->getId();
        $dto->name = $role->getName();
        $dto->description = $role->getDescription();
        return $dto;
    }

    public function mapRole(RoleResponseDto $dto): Role
    {
        $role = new Role( $dto->name, $dto->description);
        $role->getId();
        $role->getName();
        $role->getDescription();
        return $role;
    }
    
    public function mapUserToDto(User $user): UserResponseDto
    {
        return $user->mapUserDto();
    }
}
<?php

namespace Tests\DataFixtures\Entity;

use App\Dto\Create\RoleCreateDto;
use PHPUnit\Framework\TestCase;

class RoleDataTest
{

    public static function createRole(): RoleCreateDto 
    {
        $dto = new RoleCreateDto();
        $dto->name = 'Administrador';
        $dto->description = 'Administrador do sistema';
        
        return $dto;
    }

    public static function createRole1(): RoleCreateDto
    {
        $dto = new RoleCreateDto();
        $dto->name = 'Usuário';
        $dto->description = 'Usuário do sistema';
        
        return $dto;
    }

    public static function createRoleFixtures(): RoleCreateDto
    {
        $dto = new RoleCreateDto();
        $dto->name = 'Não cadastrado';
        $dto->description = 'Role ficticia';
        
        return $dto;
    }

    public static function updateRole(): RoleCreateDto 
    {
        $dto = new RoleCreateDto();
        $dto->name = 'UsuárioEdit';
        $dto->description = 'Usuário do sistema edit';
                
        return $dto;
    }
}
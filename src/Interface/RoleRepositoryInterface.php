<?php

namespace App\Interface;

use App\Dto\Create\RoleCreateDto;
use App\Dto\Response\RoleResponseDto;
use App\Result\ResultOperation;


interface RoleRepositoryInterface
{
    public function createRole(RoleCreateDto $role): ResultOperation;

    public function deleteRole(string $id): ResultOperation;

    public function updateRole(RoleCreateDto $role): ResultOperation;

    public function findRoleById(string $id): ?ResultOperation;

    public function findRoleByName(string $name): ?ResultOperation;

    public function getRoles(): ?array;

}
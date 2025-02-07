<?php

namespace App\Interface\Repository;

use App\Dto\Create\RoleCreateDto;
use App\Util\ResultOperation; 


interface RoleRepositoryInterface
{
    public function createRole(RoleCreateDto $role): ResultOperation;
    public function roleExists(string $name): ResultOperation;
    public function updateRole(string $id, RoleCreateDto $role): ResultOperation;
    public function findRoleById(string $id): ?ResultOperation;
    public function findRoleByName(string $name): ?ResultOperation;
    public function findRoleAll(): ?ResultOperation;
    public function deleteRole(string $id): ResultOperation;

}
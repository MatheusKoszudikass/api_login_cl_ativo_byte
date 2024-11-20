<?php

namespace App\Interface;

use App\Dto\Create\RoleCreateDto;
use App\Dto\Response\RoleResponseDto;
use App\Result\ResultOperation;


interface RoleRepositoryInterface
{

    public function addRole(RoleCreateDto $role): ResultOperation;

    public function deleteRole(string $id): ResultOperation;

    public function updateRole(RoleCreateDto $role): ResultOperation;

    public function getRoleById(string $id): ?RoleResponseDto;

    public function getRoles(): ?array;

}
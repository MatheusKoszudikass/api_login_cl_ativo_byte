<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Role;
use App\Dto\Create\UserCreateDto;
use App\Dto\Create\RoleCreateDto;
use App\Dto\UserResponseDto;

class MapperServiceCreate
{

    public function mapRoleToDto(Role $role): RoleCreateDto
    {
        $dto = new RoleCreateDto();
        $dto->name = $role->getName();
        $dto->description = $role->getDescription($dto->description);
        return $dto;
    }

    public function mapRole(RoleCreateDto $dto): Role
    {
        $role = new Role($dto->name);
        $dto->id = $role->getId();
        $dto->description = $role->getDescription($dto->description);
        return $role;
    }
    public function mapUserToDto(User $user): UserCreateDto
    {
        $dto = new UserCreateDto();
        $dto->email = $user->isEmail($dto->email);
        $dto->password = $user->getPassword();
        $dto->firstName = $user->isFirstName($dto->firstName);
        $dto->lastName = $user->isLastName($dto->lastName);
        $dto->userName = $user->isUserName($dto->userName);
        $dto->cnpjCpfRg = $user->isCnpjCpf($dto->cnpjCpfRg);
        $dto->roles = $user->getRoleNames();
        return $dto;
    }

    public function mapUser(UserCreateDto $dto): User
    {
        $user = new User(
            $dto->email,
            $dto->password,
            $dto->firstName,
            $dto->lastName,
            $dto->userName,
            $dto->cnpjCpfRg,
        );
        $expiresAt = (new \DateTimeImmutable())->modify('+1 days');
        // $expiresAt = new \DateTimeImmutable('+1 minutes');
        $user->getTwoFactorExpiresAt($expiresAt);
        $user->setTwoFactorToken( $dto->token);
        foreach ($dto->roles as $roleName) {
            $role = new Role($roleName);
        }

        return $user;
    }
}

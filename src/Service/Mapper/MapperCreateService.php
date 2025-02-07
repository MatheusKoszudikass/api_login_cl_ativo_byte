<?php

namespace App\Service\Mapper;

use App\Entity\User;
use App\Entity\Role;
use App\Entity\Image;
use App\Dto\Create\UserCreateDto;
use App\Dto\Create\RoleCreateDto;
use App\Dto\Create\ImageCreateDto;

class MapperCreateService
{

    public function mapRoleToDto(Role $role): RoleCreateDto
    {
        $dto = new RoleCreateDto();
        $dto->name = $role->getName();
        $dto->description = $role->getDescription();
        return $dto;
    }

    public function mapRole(RoleCreateDto $dto): Role
    {
        return new Role($dto->name, $dto->description);
    }
    public function mapUserToDto(User $user): UserCreateDto
    {
        $dto = new UserCreateDto();
        $dto->email = $user->getEmail();
        $dto->password = $user->getPassword();
        $dto->firstName = $user->getFirstName();
        $dto->lastName = $user->getLastName();
        $dto->userName = $user->getUserName();
        $dto->cnpjCpfRg = $user->getCnpjCpfRg();
        $dto->roles = $user->getRoles();
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
        
        $user->getTwoFactorExpiresAt();

        return $user;
    }

    public function mapImageToDto(Image $image): ImageCreateDto
    {
        $dto = new ImageCreateDto();
        $dto->name = $image->getName();
        $dto->path = $image->getPath();
        $dto->typeImage = $image->getTypeImage();
        $dto->ownerClass = $image->getOwnerClass();
        $dto->ownerId = $image->getOwnerId();
        return $dto;
    }

    public function mapImage(ImageCreateDto $dto): Image
    {
        $image = new Image(
            $dto->name,
            $dto->path,
            $dto->typeImage,
            $dto->ownerClass,
            $dto->ownerId,
        );
        return $image;
    }
}

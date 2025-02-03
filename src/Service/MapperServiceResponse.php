<?php

namespace App\Service;

use App\Dto\Response\RoleResponseDto;
use App\Dto\Response\UserResponseDto;
use App\Dto\Response\ImageResponseDto;
use App\Result\ResultOperation;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;

use App\Entity\Enum\TypeImageEnum;

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

    public function mapRole(ResultOperation $response): Role
    {
        $dto = $response->getData()[0];

        $role = new Role( $dto->name, $dto->description);
        $role->getId();
        $role->getName();
        $role->getDescription();
        return $role;
    }
    
    public function mapUserToDto(User $user): UserResponseDto
    {
        $dto = new UserResponseDto();
        $dto->id = $user->getId();
        $dto->email = $user->getEmail();
        $dto->firstName = $user->getFirstName();
        $dto->lastName = $user->getLastName();
        $dto->userName = $user->getUserName();
        $dto->cnpjCpfRg = $user->getCnpjCpfRg();
        $dto->legalRegister = $user->getLegalRegister();
        $dto->roles = $user->getRoles();
        return $dto;
    }

    public function mapUser(UserResponseDto $userDto): User
    {
        return new User(
            $userDto->email,
            '123456789',
            $userDto->firstName,
            $userDto->lastName,
            $userDto->userName,
            $userDto->cnpjCpfRg
        );
    }

    public function mapImageDto(Image $image): ImageResponseDto
    {
        $dto = new ImageResponseDto();
        $dto->id = $image->getId();
        $dto->name = $image->getName();
        $dto->path = $image->getPath();
        $dto->typeImage = $image->getTypeImage()->value;
        $dto->ownerClass = $image->getOwnerClass();
        $dto->ownerId = $image->getOwnerId();
        return $dto;
    }

    public function mapImage(ImageResponseDto $imageDto): Image
    {
        return new Image(
            $imageDto->name,
            $imageDto->path,
            TypeImageEnum::from($imageDto->typeImage),
            $imageDto->ownerClass,
            $imageDto->ownerId,
        );
    }
}
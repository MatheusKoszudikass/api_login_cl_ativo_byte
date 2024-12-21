<?php

namespace Tests\DataFixtures\Entity;
use App\Dto\Create\UserCreateDto;

class UserDataTest 
{
    public static function createUser(): UserCreateDto
    {
        $dto = new UserCreateDto();
        $dto->email = 'testuser1@example.com';
        $dto->password ='password123';
        $dto->firstName ='Test';
        $dto->lastName = 'User1';
        $dto->userName ='testuser1';
        $dto->cnpjCpfRg = '12345678901';
        return $dto;
    }

    public static function createUser1(): UserCreateDto
    {
        $dto = new UserCreateDto();
        $dto->email = 'testuser2@example.com';
        $dto->password ='password1234';
        $dto->firstName ='Test';
        $dto->lastName = 'User2';
        $dto->userName ='testuser2';
        $dto->cnpjCpfRg = '12345678902';
        return $dto;

    }

    public static function updateUser(): UserCreateDto
    {
        $dto = new UserCreateDto();
        $dto->email = 'testuser1edit@example.com';
        $dto->firstName = 'TestEdit';
        $dto->lastName = 'User1Edit';
        $dto->userName ='testuser1Edit';
        $dto->cnpjCpfRg = '12345678902';
        return $dto;
    }
}
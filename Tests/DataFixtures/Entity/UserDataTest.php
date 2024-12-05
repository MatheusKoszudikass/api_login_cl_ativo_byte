<?php

use App\Dto\Create\UserCreateDto;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;


class UserDataTest 
{
    public static function createUser(): UserCreateDto
    {
        $dto = new UserCreateDto();
        $dto->email = 'testuser1@example.com';
        $dto->password ='password123'; // Utilize hashing no construtor do User.
        $dto->firstName ='Test';
        $dto->lastName = 'User1';
        $dto->userName ='testuser1';
        $dto->cnpjCpfRg = '12345678901';
        return $dto;

    }
}
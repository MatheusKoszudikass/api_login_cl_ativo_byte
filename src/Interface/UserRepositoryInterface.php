<?php 

namespace App\Interface;

use App\Dto\Response\UserResponseDto;
use App\Dto\Create\UserCreateDto;
use App\Result\ResultOperation;

interface UserRepositoryInterface
{
    public function addUser(UserCreateDto $user): ResultOperation;
    public function verifyTwoFactorTokenAndEnable(string $token): ResultOperation;
    public function sentMessageCreateUser(string $firstName, string $token): string;
    public function updateUser(UserResponseDto $user): ResultOperation;
    public function deleteUser(string  $id): ResultOperation;
    public function getUserById(string  $id): ?UserResponseDto;
    public function getUserByEmail(string $email): ?UserResponseDto;
    public function getUserByUserName(string $userName): ?UserResponseDto;
    public function getUserByCnpjCpfRg(string $cnpjCpfRg): ?UserResponseDto;
    public function loginUser(string $email, string $password): ResultOperation;
    public function requestPasswordReset(string $email): ResultOperation;
    public function resetPassword(string $token, string $password): ResultOperation;
}
<?php

namespace App\Interface\Service;

use App\Dto\Create\UserCreateDto;
use App\Entity\User;
use App\Util\ResultOperation;

interface UserServiceInterface
{
    public function createUser(UserCreateDto $user): ResultOperation;

    public function enableTwoFactorAuth(string $token): ResultOperation;

    public function sendWelcomeMessage(string $firstName, string $token): string;

    public function sendPasswordRecoveryMessage(string $firstName, string $token): string;

    public function validateUser(UserCreateDto $userCreateDto): ResultOperation;

    public function userExists(string $identifier): ResultOperation;

    public function verifyTwoTokenFactorExpired(User $user, string $email): bool;

    public function verifyTokenRecoveryAccount(string $token): bool;

    public function verifyTokenExpiredRecoveryAccount(User $user): bool;

    public function updateUser(string $id, UserCreateDto $user): ResultOperation;

    public function deleteUserById(string $id): ResultOperation;

    public function findUserJwt(string $token): ResultOperation;

    public function findUserByEmailOrUsername(string $identifier): ?User;

    public function findUserById(string $id): ?ResultOperation;

    public function findUserByEmail(string $email): ?ResultOperation;

    public function findUserByUserName(string $userName): ?ResultOperation;

    public function findUserByDocument(string $document): ?ResultOperation;

    public function confirmPasswordReset(string $token, string $password): ResultOperation;
}


<?php 

namespace App\Interface;

use App\Dto\Response\UserResponseDto;
use App\Dto\Create\UserCreateDto;
use App\Entity\User;
use App\Result\ResultOperation;

interface UserRepositoryInterface
{
    public function createUser(UserCreateDto $user): ResultOperation; // Adicionar usuário
    public function enableTwoFactorAuth(string $token): ResultOperation; // Verificar e habilitar 2FA
    public function sendWelcomeMessage(string $firstName, string $token): string; // Enviar mensagem de boas-vindas
    public function sendPasswordRecoveryMessage(string $firstName, string $token): string; // Enviar mensagem para redefinir senha
    public function validateUser(UserCreateDto $userCreateDto): ResultOperation; // Validar dados do usuário
    public function userExists(string $identifier): ResultOperation; // Verificar se o usuário existe
    public function verifyTwoTokenFactorExpired(User $user, string $email): bool; // Verificar se o token  de dois fatores expirou
    public function verifyTokenRecoveryAccount(string $token): bool; // Verificar token de redefinição de senha
    public function verifyTokenExpiredRecoveryAccount(User $user): bool; // Verificar se o token de redefinição de senha expirou
    public function updateUser(string $id, UserCreateDto $user): ResultOperation; // Atualizar informações do usuário
    public function deleteUserById(string $id): ResultOperation; // Remover usuário por ID
    public function findUserJwt(string $token): ResultOperation; // Buscar usuário por token JWT
    public function findUserByEmailOrUsername(string $identifier): ?User; // Buscar usuário por email ou nome de usuário
    public function findUserById(string $id): ?ResultOperation; // Buscar usuário por ID
    public function findUserByEmail(string $email): ?ResultOperation; // Buscar usuário por email
    public function findUserByUserName(string $userName): ?ResultOperation; // Buscar usuário por nome de usuário
    public function findUserByDocument(string $document): ?ResultOperation; // Buscar usuário por CPF/CNPJ/RG
    public function confirmPasswordReset(string $token, string $password): ResultOperation; // Redefinir senha
}

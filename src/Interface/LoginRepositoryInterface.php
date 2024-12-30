<?php

namespace App\Interface;

use App\Dto\LoginDto;
use App\Entity\Login;
use App\Result\ResultOperation;

interface LoginRepositoryInterface
{
    public function login(LoginDto $login): ResultOperation;
    public function findUserJwt(string $token): ResultOperation;
    public function recoveryAccount(string $email_username): ResultOperation;
    public function validateTokenJwt(string $token): ResultOperation;
}
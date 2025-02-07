<?php

namespace App\Interface\Service;

use App\Dto\LoginDto;
use App\Util\ResultOperation;

interface LoginServiceInterface
{
    public function login(LoginDto $login): ResultOperation;
    public function recoveryAccount(string $email_username): ResultOperation;
    public function validateTokenJwt(string $token): ResultOperation;
}

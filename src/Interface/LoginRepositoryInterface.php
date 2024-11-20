<?php

namespace App\Interface;

use App\Dto\LoginDto;
use App\Entity\Login;
use App\Result\ResultOperation;

interface LoginRepositoryInterface
{
    public function addLogin(LoginDto $login): ResultOperation;

    public function validadteTokenJwt(string $token): ResultOperation;
    public function updateLogin(LoginDto $login): ResultOperation;
}
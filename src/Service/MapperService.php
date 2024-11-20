<?php

namespace App\Service;

use App\Entity\Login;
use App\Dto\LoginDto;

class MapperService
{

    public function mapLogin(LoginDto $dto): Login
    {
        $login = new Login(
            $dto->email_userName,
            $dto->password,
            $dto->lastLoginIp
        );
        
        return $login;
    }

    public function mapLoginDto(Login $login): LoginDto
    {
        $dto = new LoginDto();
        $dto->email_userName = $login->getEmailUserName();
        $dto->password = $login->getPasswordHash();
        $dto->lastLoginIp = $login->getLastLoginIp();

        return $dto;
    }
}
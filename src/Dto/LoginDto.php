<?php

namespace App\Dto;

use phpDocumentor\Reflection\Types\Boolean;

class LoginDto
{
    public string $email_userName;
    public string $password;
    public bool $remember;
    public string $lastLoginIp;
}
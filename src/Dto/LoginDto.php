<?php

namespace App\Dto;

class LoginDto
{
    public string $emailUserName = '';
    public string $password = '';
    public bool $remember = false;
    public string $lastLoginIp = '';
}
<?php

namespace App\Dto;

class LoginDto extends BaseEntityDto
{
    public string $emailUserName = '';
    public string $password = '';
    public bool $remember = false;
    public string $lastLoginIp = '';
}
<?php

namespace App\Service;

use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use stdClass;

class TwoFactorAuthService
{
    private TokenGeneratorInterface $_tokenGeneratorInterface;

    public function __construct(TokenGeneratorInterface $tokenGeneratorInterface)
    {
        $this->_tokenGeneratorInterface = $tokenGeneratorInterface;
    }
    
    public function generateToken(): string
    {
        return $this->_tokenGeneratorInterface->generateToken();
    }

    public function generateTokenJwt(array $payload, bool $remember): string
    {
        $issuedAt = time();
        $expire = $remember ? $issuedAt + 60 * 60 * 24 * 30 : $issuedAt + 60 * 60;

        $payload['iat'] = $issuedAt;
        $payload['exp'] = $expire;

        return JWT::encode($payload, $_SERVER['JWT_SECRET'], 'HS256');
    }

    public function verifyToken(string $token): stdClass
    {
        return JWT::decode(
            $token, new Key($_SERVER['JWT_SECRET'], 'HS256'));
    }

    public function invalidatingToken(string $token): string
    {
         return '';
    }
}
<?php

namespace App\Service\Auth;

use App\Util\PayLoadJwt;
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

    public function generateTokenJwt(PayLoadJwt $payload, bool $remember): string
    {
        if(empty($payload)) return '';
        
        $issuedAt = time();
        $expire = self::calculateExpireTimeToken($remember, $issuedAt);

        $payload->addProperty('iat', $issuedAt);
        $payload->addProperty('exp', $expire);

        return JWT::encode($payload->getPayload(), $_SERVER['JWT_SECRET'], 'HS256');
    }

    private function calculateExpireTimeToken(bool $remember, int $issuedAt): int
    {
        return $remember ? $issuedAt + 60 * 60 * 24 * 30 : $issuedAt + 60 * 60;
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
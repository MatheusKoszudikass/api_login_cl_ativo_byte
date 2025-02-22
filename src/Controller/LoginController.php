<?php

namespace App\Controller;

use App\Dto\LoginDto;
use App\Interface\Service\LoginServiceInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractController
{
    private LoginServiceInterface $_loginRepostory;
    public function __construct(LoginServiceInterface $loginRepostory)
    {
        $this->_loginRepostory = $loginRepostory;
    }

    /**
     * Performs user login and returns a JWT token
     *
     * @param LoginDto $login Dada login
     * @param Request $request Request
     *
     * @return JsonResponse Response token JWT
     *
     * @throws Exception if the email or password is incorrect or if the user is deactivated.
     */
    #[Route('/api/auth/login', methods: ['POST'], name: 'login')]
    public function Login(#[MapRequestPayload] LoginDto $login, Request $request): JsonResponse
    {
        $login->lastLoginIp = $request->getClientIp();

        $result = $this->_loginRepostory->login($login);

        if (!empty($result->getData()) && $result->getData()[0] !== '') {
            $token = $result->getData()[0];
            $result->clearData();

            return $this->json($result, 200, ['Content-Type' => 'application/json', 
            'set-cookie' => $this->createCookie($token, $login->remember)]);

        }
        $result->clearData();
        return $this->json($result, 200);
    }

    /**
     * Creates a cookie from a given token.
     *
     * @param string $token
     *
     * @return Cookie
     */
    private function createCookie(string $token, bool $remember): Cookie
    {
        $expire = $remember ? time() + 60 * 60 * 24 * 30 : 0;

        return new Cookie(
            $_ENV['COOKIE_NAME'],                            
            $token,                                        
            $expire,                                     
            $_ENV['COOKIE_PATH'],                         
            $_ENV['COOKIE_DOMAIN'],                    
            $_ENV['COOKIE_SECURE'],                   
            $_ENV['COOKIE_HTTP_ONLY'],             
            $_ENV['COOKIE_RAW'],                       
            $_ENV['COOKIE_SAMESITE']             
        );
    }
    
    /**
     * Verifies a token JWT and returns a boolean indicating if the token is valid or not.
     * 
     * @param Request $request The request with the token to be verified.
     * @return JsonResponse A JSON response with a boolean indicating if the token is valid or not.
     * @throws Exception If the token is invalid.
     */
    #[Route('/api/auth/verify', methods: ['GET'], name: 'login_verify')]
    public function verifyToken(Request $request): JsonResponse
    {
        $token = $request->cookies->get('session');

        if ($token != null) {

            try {
                $this->_loginRepostory->validateTokenJwt($token); 
                return $this->json(true, 200);
            } catch (Exception $e) {
                return $this->json(false, 400);
            }
        }
        return $this->json(false, 200);
    }

    #[Route ('/api/auth/logout', methods: ['GET'], name: 'login_logout')]
    public function logout(Request $request): JsonResponse
    {
        $token = $request->cookies->get('session');
        
        if ($token != null) {
           $result = $this->_loginRepostory->validateTokenJwt($token);

           if($result->isSuccess() == true) {
            $response = new JsonResponse();
            $response->headers->clearCookie($_ENV['COOKIE_NAME'], '/', $_ENV['COOKIE_DOMAIN']);
            return $response->setData([
                'message' => 'Logout ',
                'status' => $result->isSuccess()
            ]);
           }
        }
        return $this->json(false, 200);
    }

    /**
     * Recovery account using email.
     * 
     * This action recovers an account by sending an email with a link to reset the password.
     * The link is valid for a short period of time and can only be used once.
     * 
     * @param LoginDto $loginDto The login dto with the email of the user to be recovered.
     * @return JsonResponse A json response with the result of the operation.
     */

    #[Route('/api/auth/recovery-account', methods: ['POST'], name: 'login_recovery')]
    public function recoveryAccount(#[MapRequestPayload] LoginDto $loginDto): JsonResponse
    {
        $result = $this->_loginRepostory->recoveryAccount($loginDto->emailUserName);

        return $this->json($result, 200);
    }
}

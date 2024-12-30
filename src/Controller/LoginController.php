<?php

namespace App\Controller;

use App\Dto\LoginDto;
use App\Interface\LoginRepositoryInterface;
use App\Result\ResultOperation;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractController
{
    private LoginRepositoryInterface $_loginRepostory;
    public function __construct(LoginRepositoryInterface $loginRepostory)
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

            $response = new JsonResponse();
            $response->headers->setCookie($this->createCookie($token, $login->remember));
            $response->setData([
                'message' => $result->getMessage(),
                'status' => $result->isSuccess()
            ]);

            return $response;
        } else {
            $response = new JsonResponse();
            $response->setData([
                'message' => $result->getMessage(),
                'status' => $result->isSuccess()
            ]);

            return $response;
        }
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
            $_ENV['COOKIE_NAME'],                            // Cookie name
            $token,                                        // Token value
            $expire,                                     // Expiration time(1, for example)
            $_ENV['COOKIE_PATH'],                         // Cookie path, where it will be accessible("/" means accessible throughout the application)
            $_ENV['COOKIE_DOMAIN'],                    // Cookie domain(leave null for the current domain)
            $_ENV['COOKIE_SECURE'],                   // Secure: send only if using HTTTPS
            $_ENV['COOKIE_HTTP_ONLY'],             // HttpOnly: ensure that the cookie is not accessed by javaScript 
            $_ENV['COOKIE_RAW'],                       // SameSite: defines whether the cookie can be sent with cross-site requests
            $_ENV['COOKIE_SAMESITE']             // Raw: if you want to send the value as is (without coding)
        );
    }
    
    #[Route('/api/auth/findUser', methods: ['GET'], name: 'login_findUser')]	
    public function findUser(Request $request): JsonResponse
    {
        $token = $request->cookies->get('session');

        if ($token != null) {
            $result = $this->_loginRepostory->findUserJwt($token);
            return $this->json($result, 200);
        }

        return $this->json(false, 200);
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
        $result = $this->_loginRepostory->recoveryAccount($loginDto->email_userName);

        return $this->json($result, 200);
    }
}

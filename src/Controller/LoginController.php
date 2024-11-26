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
use Symfony\Component\Validator\Tests\Fixtures\ToString;

class LoginController extends AbstractController
{
    private LoginRepositoryInterface $_loginRepostory;
    public function __construct(LoginRepositoryInterface $loginRepostory)
    {
        $this->_loginRepostory = $loginRepostory;
    }

    #[Route('/api/login', methods: ['POST'], name: 'login')]
    public function Login(#[MapRequestPayload] LoginDto $login, Request $request): JsonResponse
    {
        $login->lastLoginIp = $request->getClientIp();

        $result = $this->_loginRepostory->login($login);

        // Verifica se getData() não está vazio antes de acessar o índice [0]
        if (!empty($result->getData()) && $result->getData()[0] !== '') {
            $token = $result->getData()[0];
            $cookie = new Cookie(
                'JWT',                       // Nome do cookie 
                $token,                      // Valor do token
                time() + 86400,                // Tempo de expiração (1 hora, por exemplo)
                '/',                         // Caminho do cookie, onde estará acessível ("/" significa acessível em toda a aplicação)
                null,                     // Domínio do cookie (deixe null para o domínio atual)
                false,                        // Secure: enviar apenas se estiver usando HTTPS
                true,                     // HttpOnly: garantir que o cookie não seja acessado via JavaScript
                false,                        // SameSite: define se o cookie deve ser enviado junto com requisições cross-site (defina conforme sua necessidade)
                false                   // Raw: caso queira enviar o valor como está (sem codificação)
            );

            $response = new JsonResponse();
            $response->headers->setCookie($cookie);
            $response->setData([
                'message' => $result->getMessage(),
                'status' => $result->isSuccess()
            ]);

            return $response;
        } else {
            // Se getData() estiver vazio ou o índice [0] estiver vazio, envia uma resposta sem cookie
            $response = new JsonResponse();
            $response->setData([
                'message' => $result->getMessage(),
                'status' => $result->isSuccess()
            ]);

            return $response;
        }
    }

    #[Route('/api/auth/verify', methods: ['POST'], name: 'login_verify')]
    public function verifyToken(Request $request): JsonResponse
    {
        $token = $request->cookies->get('JWT');

        if ($token) {
            // Verifica o token
            try {
                $this->_loginRepostory->validadteTokenJwt($token); // Método que valida o token
                return $this->json([true]);
            } catch (Exception $e) {
                return $this->json([false], 400);
            }
        }
        return $this->json([false], 400);
    }

    #[Route('/api/auth/recovery', methods: ['POST'], name: 'login_recovery')]
    public function recoveryAccount(#[MapRequestPayload] LoginDto $loginDto): JsonResponse
    {
        $result = $this->_loginRepostory->recoveryAccount($loginDto->email_userName);

        return $this->json($result, 200);
    }
}

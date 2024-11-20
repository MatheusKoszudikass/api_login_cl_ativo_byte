<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Interface\UserRepositoryInterface;
use App\Dto\Create\UserCreateDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

class UserController extends AbstractController
{
    private UserRepositoryInterface $_userRepositoryInterface;

    public function __construct(UserRepositoryInterface $userRepositoryInterface)
    {
        $this->_userRepositoryInterface = $userRepositoryInterface;
    }


     #[Route('/api/user/add', name: 'api_user_add')]
     public function addUser(#[MapRequestPayload] UserCreateDto $userDto): JsonResponse
     {
         $result = $this->_userRepositoryInterface->addUser($userDto);
         
         return $this->json($result);
     }

    #[Route('/api/user/active-user', name: 'api_user_verify')]
    public function verify(Request $request): JsonResponse
    {
         // Obter o token via query string
         $token = $request->query->get('token');

         if (!$token) {
             return new Response('Token não fornecido', Response::HTTP_BAD_REQUEST);
         }
 
         // Verificar se o token é válido e ativar o Two-Factor Authentication
         $result = $this->_userRepositoryInterface->verifyTwoFactorTokenAndEnable($token);

         return $this->json($result);

    }
    #[Route('/api/user/update', name: 'api_user_update')]
    public function updateUser(Request $request): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
        ]);
    }

    #[Route('/api/user/delete', name: 'api_user_delete')]
    public function deleteUser(Request $request): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    #[Route('/api/user/get', name: 'api_user_get')]
    public function user(Request $request): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    #[Route('/api/user/getAll', name: 'api_user_getAll')]
    public function users(Request $request): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    #[Route('/api/user/login', name: 'api_user_login')]
    public function login(Request $request): JsonResponse
    {
        $email = $request->get('email');
        $password = $request->get('password');

        $result = $this->_userRepositoryInterface->loginUser($email, $password);

        if (!$result->isSuccess()) {
            return new JsonResponse(['error' => $result->getMessage()], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse(['message' => $result->getMessage(), 'token' => $result->getData()['token']], Response::HTTP_OK);
    }

    #[Route('/api/user/logout', name: 'api_user_logout')]
    public function logout(Request $request): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    #[Route('/api/user/requestPasswordReset', name: 'api_user_requestPasswordReset')]
    public function requestPasswordReset(Request $request): JsonResponse
    {
        $email = $request->get('email');

        $result = $this->_userRepositoryInterface->requestPasswordReset($email);

        return new JsonResponse(['message' => $result->getMessage()]);
    }

    #[Route('/api/user/resetPassword', name: 'api_user_resetPassword')]
    public function resetPassword(Request $request): JsonResponse
    {
        $token = $request->get('token');
        $password = $request->get('password');

        $result = $this->_userRepositoryInterface->resetPassword($token, $password);

        return new JsonResponse(['message' => $result->getMessage()]);
    }
}


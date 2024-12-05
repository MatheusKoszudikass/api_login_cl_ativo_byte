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

    /**
     * Add a new user to the system.
     *
     * @param UserCreateDto $userDto The user data to be added.
     * @return JsonResponse The result of the operation.
     */
     #[Route('/api/user/add', methods: ['POST'], name: 'api_user_add')]
     public function addUser(#[MapRequestPayload] UserCreateDto $userDto): JsonResponse
     {
         $result = $this->_userRepositoryInterface->createUser($userDto);
         
         return $this->json($result);
     }


/*************  ✨ Codeium Command ⭐  *************/
    /**
     * Enable two factor authentication for the user given the token.
     *
     * @param Request $request The request with the token to be verified.
     * @return JsonResponse A JSON response with the result of the operation.
     */
/******  a4dc5bde-ebc2-49dc-a9a0-44fdedb3ad76  *******/    #[Route('/api/user/active-user', methods: ['POST'], name: 'api_user_verify')]
    public function verify(Request $request): JsonResponse
    {
         $token = $request->query->get('token');

         if (!$token) {
             return $this->json(['message' => 'Token não encontrado'], Response::HTTP_BAD_REQUEST);
         }
 
         $result = $this->_userRepositoryInterface->enableTwoFactorAuth($token);

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

    #[Route('/api/user/logout', name: 'api_user_logout')]
    public function logout(Request $request): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/UserController.php',
        ]);
    }

    /**
     * Resets the password for the user with the given token and new password.
     *
     * @param Request $request The request with the token and new password.
     *
     * @return JsonResponse A JSON response with the result of the operation.
     */
    #[Route('/api/user/resetPassword', name: 'api_user_resetPassword')]
    public function resetPassword(Request $request): JsonResponse
    {
        $token = $request->get('token');
        $password = $request->get('password');

        $result = $this->_userRepositoryInterface->confirmPasswordReset($token, $password);

        return $this->json([$result]);
    }
}


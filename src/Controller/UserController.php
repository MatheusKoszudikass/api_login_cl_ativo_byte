<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Interface\UserRepositoryInterface;
use App\Dto\Create\UserCreateDto;
use App\Dto\RecoveryAccount;
use Symfony\Component\HttpFoundation\Request;
use App\Result\ResultOperation;
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
    public function add(#[MapRequestPayload] UserCreateDto $userDto): JsonResponse
    {
        $result = $this->_userRepositoryInterface->createUser($userDto);

        return $this->json($result);
    }

    /**
     * Enable two factor auth for the user.
     *
     * @param Request $request The request with the token to be verified.
     * @return JsonResponse The result of the operation.
     */
    #[Route('/api/user/twoFactorAuth', methods: ['POST'], name: 'api_user_twoFactorAuth')]
    public function twoFactorAuth(Request $request): JsonResponse
    {
        $token = $request->query->get('token');

        return $this->json($this->_userRepositoryInterface->enableTwoFactorAuth($token));
    }

    /**
     * Validates a user.
     *
     * @param UserCreateDto $userDto The user data to be validated.
     * @return JsonResponse The result of the operation.
     */
    #[Route('/api/user/validade', methods: ['POST'], name: 'api_user_validadeUser')]
    public function validade(#[MapRequestPayload] UserCreateDto $userDto): JsonResponse
    {
        return $this->json($this->_userRepositoryInterface->validateUser($userDto));
    }

    #[Route('/api/user/exist', methods: ['POST'], name: 'api_user_verifyUserExist')]
    public function verifyUserExist(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        $identifier = $payload['identifier'] ?? null;
    
        if ($identifier === null || $identifier === '') {
            return $this->json(new ResultOperation(false, 'Identificador null.'));
        }
    
        // Verificação do usuário
        $exists = $this->_userRepositoryInterface->userExists($identifier);
    
        return $this->json($exists);
    }

    /**
     * Update a user.
     *
     * @param Request $request The request with the id parameter.
     * @param UserCreateDto $userDto The user data to be updated.
     * @return JsonResponse The result of the operation.
     */
    #[Route('/api/user/update', methods: ['PUT'],  name: 'api_user_resetPassword')]
    public function update(Request $request, #[MapRequestPayload] UserCreateDto $userDto): JsonResponse
    {
        $id = $request->query->get('id');

        return $this->json($this->_userRepositoryInterface->updateUser($id, $userDto));
    }

    /**
     * Delete a user by ID.
     *
     * @param Request $request The request with the id parameter.
     * @return JsonResponse The result of the operation.
     */
    #[Route('/api/user/delete', methods: ['DELETE'], name: 'api_user_delete')]
    public function delete(Request $request): JsonResponse
    {
        $token = $request->query->get('id');

        return $this->json($this->_userRepositoryInterface->deleteUserById($token));
    }


    /**
     * Finds a user by their JWT token from the cookie.
     *
     * @param Request $request The HTTP request containing the JWT token in the 'session' cookie.
     * @return JsonResponse A JSON response with the result of the operation.
     */
    #[Route('/api/user/findUserSession', methods: ['GET'], name: 'login_findUserSession')]	
    public function findUser(Request $request): JsonResponse
    {
        $token = $request->cookies->get('session');

        if ($token != null) 
        {
            $result = $this->_userRepositoryInterface->findUserJwt($token);
            return $this->json($result, 200);
        }

        return $this->json(new ResultOperation(false, 'Usuário nao encontrado'), 200);
    }
    /**
     * Find a user by ID.
     *
     * Retrieves a user based on the provided ID parameter from the request query.
     *
     * @param Request $request The HTTP request containing the ID parameter.
     * @return JsonResponse A JSON response with the result of the operation.
     */

    #[Route('/api/user/findId', methods: ['GET'], name: 'api_user_get')]
    public function findById(Request $request): JsonResponse
    {
        $id = $request->query->get('id');

        return $this->json($this->_userRepositoryInterface->findUserById($id));
    }

    /**
     * Finds a user by their email.
     *
     * Retrieves a user based on the provided email parameter from the request query.
     *
     * @param Request $request The HTTP request containing the email parameter.
     * @return JsonResponse A JSON response with the result of the operation.
     */
    #[Route('/api/user/findEmail', methods: ['GET'], name: 'api_user_get')]
    public function findByEmail(Request $request): JsonResponse
    {
        $email = $request->query->get('email');

        return $this->json($this->_userRepositoryInterface->findUserByEmail($email));
    }

    /**
     * Finds a user by their username.
     *
     * Retrieves a user based on the provided username parameter from the request query.
     *
     * @param Request $request The HTTP request containing the username parameter.
     * @return JsonResponse A JSON response with the result of the operation.
     */
    #[Route('/api/user/findUserName', methods: ['GET'], name: 'api_user_get')]
    public function findByUserName(Request $request): JsonResponse
    {
        $userName = $request->query->get('userName');

        return $this->json($this->_userRepositoryInterface->findUserByUserName($userName));
    }

    /**
     * Finds a user by their document (CPF/CNPJ/RG).
     *
     * Retrieves a user based on the provided document parameter from the request query.
     *
     * @param Request $request The HTTP request containing the document parameter.
     * @return JsonResponse A JSON response with the result of the operation.
     */
    #[Route('/api/user/findDocument', methods: ['GET'], name: 'api_user_get')]
    public function findByDocument(Request $request): JsonResponse
    {
        $document = $request->query->get('document');

        return $this->json($this->_userRepositoryInterface->findUserByDocument($document));
    }

    /**
     * Verifies a user password recovery token.
     *
     * Checks if the provided token is valid and not expired.
     *
     * @param Request $request The HTTP request containing the token parameter.
     * @return JsonResponse A JSON response with the result of the operation. True if the token is valid, false otherwise.
     */
    #[Route("/api/user/verifyTokenRecoveryAccount", methods: ['GET'], name: 'api_user_verifyTokenRecoveryAccount')]
    public function verifyTokenRecoveryAccount(Request $request): JsonResponse
    {
        $token = $request->query->get('token');

        if(empty($token)) return $this->json(false, 200);

        return $this->json($this->_userRepositoryInterface->verifyTokenRecoveryAccount($token));
    }

    /**
     * Confirms the password reset for a user with the given token and new password.
     *
     * Retrieves a user based on the provided token and password parameters from the request query,
     * validates the provided token and password, checks if the token is valid and not expired,
     * and updates the user's password if all checks pass. Resets the password reset token and its expiration date.
     *
     * @param Request $request The HTTP request containing the token and password parameters.
     * @return JsonResponse A JSON response with the result of the operation.
     */
    #[Route('/api/user/confirmPasswordReset', methods: ['POST'], name: 'api_user_confirmPasswordReset')]
    public function confirmPasswordReset(#[MapRequestPayload] RecoveryAccount $recovery): JsonResponse
    {
        if($recovery == null) return $this->json(new ResultOperation(
            false, 'Token nao pode ser null'));
            
        return $this->json(
            $this->_userRepositoryInterface->confirmPasswordReset(
                $recovery->token, $recovery->password));
    }
}

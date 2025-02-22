<?php

namespace App\Service\User;

use App\Dto\Create\UserCreateDto;
use App\Dto\Response\UserResponseDto;
use App\Entity\Enum\TypeEntitiesEnum;
use App\Entity\Role;
use App\Entity\User;
use App\Interface\Service\UserServiceInterface;
use App\Repository\BaseRepository;
use App\Service\Auth\TwoFactorAuthService;
use App\Service\Email\EmailService;
use App\Service\Mapper\MapperCreateService;
use App\Service\Mapper\MapperResponseService;
use App\Service\Util\ResultOperationService;
use App\Util\DoctrineFindParams;
use App\Util\ResultOperation;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

class UserService extends BaseRepository implements UserServiceInterface
{
    private MapperCreateService $_mapperCreateService;
    private MapperResponseService $_mapperResponseService;
    private ResultOperationService $_resultOperationService;
    private TwoFactorAuthService $_twoFactorAuthService;
    private EmailService $_emailService;

    public function __construct(
        ManagerRegistry $doctrine,
        MapperCreateService $mapperCreateService,
        MapperResponseService $mapperResponseService,
        ResultOperationService $resultOperationService,
        TwoFactorAuthService $twoFactorAuthService,
        EmailService $emailService

    ) {
        parent::__construct($doctrine, TypeEntitiesEnum::USER);
        $this->_mapperCreateService = $mapperCreateService;
        $this->_mapperResponseService = $mapperResponseService;
        $this->_resultOperationService = $resultOperationService;
        $this->_twoFactorAuthService = $twoFactorAuthService;
        $this->_emailService = $emailService;
    }

    public function createUser(UserCreateDto $userDto): ResultOperation
    {
        if ($userDto == null || $userDto->isEmpty()) return $this->_resultOperationService->createFailure(
            'Usuário não pode ser nulo'
        );

        $resultVerify = $this->verifyIfUserHasExist($userDto);

        if (
            $resultVerify instanceof ResultOperation &&
            $resultVerify->isSuccess() == false
        ) return $resultVerify;

        $user = $this->checkIfUserForCreationExists($userDto);

        $this->setUserTwoFactorExpiration($user);

        $this->persist($user);

        $this->sendWelcomeEmail($user);

        return $this->_resultOperationService->createSuccess(
            'Usuário criado com sucesso. Verifique sua caixa de email para ativação da conta.'
        );
    }

    private function verifyIfUserHasExist(UserCreateDto $userDto): Object
    {
        $result = $this->findUserByEmailOrUsername($userDto->email);

        if ($result != null) $this->_resultOperationService->createFailure('Usuário já existe');

        $result = $this->findUserByEmailOrUsername($userDto->userName);

        if ($result != null) return $this->_resultOperationService->createFailure('Usuário já existe');

        return $this->_resultOperationService->createSuccess('Usuário pode ser criado');
    }

    private function checkIfUserForCreationExists(UserCreateDto $userDto): Object
    {
        $token = $this->_twoFactorAuthService->generateToken();

        $user = $this->_mapperCreateService->mapUser($userDto);

        $user->setTwoFactorToken($token);

        $this->verifyAddRoleForUser($userDto, $user);

        return  $user;
    }

    private function verifyAddRoleForUser(UserCreateDto $userDto, User $user): void
    {
        foreach ($userDto->roles as $roleExistVerify) {

            if (!empty($roleExistVerify['id']) && empty($roleExistVerify['name'])) {
                $role = $this->getEntityManager()->getRepository(
                    Role::class
                )->findOneBy(['id' => $roleExistVerify['id']]);
            }

            if (empty($role)) {
                $role = $this->getEntityManager()->getRepository(
                    Role::class
                )->findOneBy(['name' => $roleExistVerify['name']]);
            }

            if (empty($role)) {
                $roleCreateDto = new Role(
                    $roleExistVerify['name'],
                    $roleExistVerify['description']
                );

                $user->addRole($roleCreateDto);
            } else {

                $user->addRole($role);
            }
        }
    }

    private function setUserTwoFactorExpiration($user): void
    {
        $user->setTwoFactorExpiresAt(
            (new \DateTimeImmutable('now'))->modify('+1 days')
        );
    }

    private function sendWelcomeEmail($user): void
    {
        $mensagem = $this->sendWelcomeMessage(
            $user->getfullName(),
            $user->getTwoFactorToken()
        );

        $this->_emailService->sendEmail(
            $user->getEmail(),
            'AtivoByte - Cadastrado',
            $mensagem
        );
    }

    public function enableTwoFactorAuth(string $token): ResultOperation
    {
        $user = $this->getEntityOneBy(
            $this->returnDoctrineFindParams('twoFactorToken', $token)
        );

        if (empty($user)) {
            return $this->_resultOperationService->createFailure('Token inválido');
        }

        if ($this->isTokenExpired($user, '')) {
            return $this->handleExpiredToken($user);
        }

        $this->activateTwoFactorAuth($user);

        return $this->_resultOperationService->createSuccess('Token verificado com sucesso');
    }

    private function handleExpiredToken($user): ResultOperation
    {
        $token = $this->_twoFactorAuthService->generateToken();
        $userDto = $this->updateToken($user, $token);
        $mensagem = $this->sentMessageCreateUser($userDto->firstName . ' ' . $userDto->lastName, $token);
        $this->_emailService->sendEmail($userDto->email, 'AtivoByte - Cadastrado', $mensagem);

        return $this->_resultOperationService->createFailure('Token expirado, verifique seu e-mail');
    }

    private function activateTwoFactorAuth($user): void
    {
        $user->setTwoFactorToken('');
        $user->setTwoFactorEnabled(true);
        $this->persist($user);
    }
    
    public function sendWelcomeMessage(string $firstName, string $token): string
    {
        $baseUrl = $_ENV['CORS_ALLOW_ORIGINS'];

        return "
        <p>Seja bem-vindo ao <strong>AtivoByte</strong>, {$firstName}!</p>
        <p>Estamos felizes em tê-lo conosco e esperamos que tenha uma ótima experiência em nossa plataforma.</p>
        <p>Para completar o seu cadastro e ativar sua conta, por favor, clique no link abaixo:</p>
        <p><a href=\"{$baseUrl}/active-user?token={$token}\">Ativar minha conta</a></p>
        <p>Se você não solicitou esse cadastro, por favor, ignore este e-mail.</p>
        <p>Atenciosamente,<br>Equipe Ativo Byte</p>
    ";
    }

    public function sendPasswordRecoveryMessage(string $firstName, string $token): string
    {
        $baseUrl = $_ENV['CORS_ALLOW_ORIGINS'];
        return "
        <p>Olá, {$firstName},</p>
        <p>Recebemos uma solicitação para redefinir sua senha na plataforma <strong>AtivoByte</strong>.</p>
        <p>Se foi você quem solicitou, clique no link abaixo para redefinir sua senha:</p>
        <p><a href=\"{$baseUrl}/reset-password?token={$token}\">Redefinir minha senha</a></p>
        <p>Este link é válido por 24 horas. Após esse período, você precisará solicitar um novo link.</p>
        <p>Se você não solicitou a recuperação de senha, por favor, ignore este e-mail. Sua conta permanecerá segura.</p>
        <p>Atenciosamente,<br>Equipe Ativo Byte</p>
    ";
    }

    public function validateUser(UserCreateDto $userDto): ResultOperation
    {
        $user = $this->findUserByEmailOrUsername($userDto->email);

        if (!$user || !$user->authenticate($userDto->password)) 
                return $this->_resultOperationService->createFailure('Email ou senha incorretos');

        $userDto = $this->_mapperResponseService->mapUserToDto($user);

        if (!$user->isTwoFactorEnabled()) {
            if ($this->_isTokenExpired($user, 'now')) 
            {
                $token = $this->_twoFactorAuthService->generateToken();
                $userDto = $this->updateToken($user, $token);
                $this->sendTwoFactorActivationEmail($userDto, $token);
            }
            return $this->_resultOperationService->createFailure(
                'Conta não ativada, verifique o email cadastrado!');
        }

        return $this->_resultOperationService->createSuccess('Usuário valídado com sucesso!');
    }
    
    private function isTokenExpired(User $user, string $value): bool
    {
        return $user->verifyTwoFactorExpiresAt(new \DateTimeImmutable($value));
    }
    
    public function userExists(string $identifier): ResultOperation
    {
        if ($identifier == null || $identifier === '') return $this->_resultOperationService->createFailure(
            'Identificador null.');

        try {
            $queryBuilder = $this->getEntityManager()->createQueryBuilder();

            $queryBuilder->select('1')
                ->from(User::class, 'u')
                ->where('u.email = :identifier OR u.userName = :identifier')
                ->setParameter('identifier', $identifier)
                ->setMaxResults(1);
            if ($queryBuilder->getQuery()->getOneOrNullResult() !== null) {
                return new ResultOperation(true, 'Usuário já existe');
            }
            return new ResultOperation(false, 'Usuário não encontrado');
        } catch (Exception $e) {
            return new ResultOperation(false, 'Erro ao buscar usuário pelo email ou userName: ' . $e->getMessage());
        }
    }
    
    public function verifyTwoTokenFactorExpired(User $user, string $email): bool
    {
        if ($user->isTwoFactorEnabled() == false) {
            if ($user->verifyTwoFactorExpiresAt(new \DateTimeImmutable('now')) == true) {

                $tokenActiveCount = $this->_twoFactorAuthService->generateToken();
                $menssage = $this->sendWelcomeMessage(
                    $user->getfullName(),
                    $tokenActiveCount
                );

                $user->setTwoFactorToken($tokenActiveCount);
                $user->setTwoFactorExpiresAt(
                    new \DateTimeImmutable('+1 days')
                );

                $this->getEntityManager()->persist($user);
                $this->getEntityManager()->flush();

                $this->_emailService->sendEmail(
                    $user->getEmail(),
                    'AtivoByte - Cadastrado',
                    $menssage,
                );

                $user->setTwoFactorToken($tokenActiveCount);
            }
            return true;
        }

        return false;
    }

    /**
     * Checks if the password recovery token exists in the database and
     * has not expired.
     *
     * It checks if the password recovery token exists in the database,
     * filtering by token and verifying if the expiration date is greater than the
     * current date.
     *
     * If the token is empty, it returns false.
     *
     * @param string $token The password recovery token to be checked.
     * @return bool True if the token exists and has not expired, false otherwise.
     * @throws Exception If an error occurs while checking the token in the database.
     */
    public function verifyTokenRecoveryAccount(string $token): bool
    {
        if (empty($token)) return false;

        try {
            return $this->verifyTokenRecoveryAccountDb($token);
        } catch (Exception $e) {
            throw new Exception("Erro ao verificar token no banco de dados.", 0, $e);
        }
    }

    private function verifyTokenRecoveryAccountDb(string $token): bool
    {
        return $this->getEntityManager()
            ->createQuery(
                "SELECT COUNT(u.id) FROM App\Entity\User u 
            WHERE u.resetPasswordToken = :token
            AND u.resetPasswordTokenExpiresAt > CURRENT_TIMESTAMP()"
            )->setParameter('token', $token)
            ->getSingleScalarResult() > 0;
    }

    /**
     * Checks if the password recovery token has expired.
     *
     * It checks if the user's password recovery token is null and
     * if the token has expired based on the User object's method.
     *
     * @param User $user The User object to be checked.
     * @return bool True if the token has expired, false otherwise.
     */
    public function verifyTokenExpiredRecoveryAccount(User $user): bool
    {
        if (
            $user->getResetPasswordTokenExpiresAt() != null &&
            $user->verifyResetPasswordTokenExpiresAt(
                new \DateTimeImmutable('now')
            ) == false
        ) {

            return true;
        }
        return false;
    }


    /**
     * Searches for a user by email or username.
     *
     * If the $identifier is an email, it searches for a user with the provided email.
     * If not found, it searches for a user with the provided username.
     *
     * @param string $identifier The email or username to be searched.
     * @return User|null The user found or null if not found.
     */
    public function findUserByEmailOrUsername(string $identifier): ?User
    {
        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['email' => $identifier]);

        if (!$user) {
            $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['userName' => $identifier]);
        }

        return $user;
    }

    /**
     * Sends an email to the user with an account activation token.
     *
     * @param UserResponseDto $userDto The user DTO that needs to activate their account.
     */
    private function sendTwoFactorActivationEmail(UserResponseDto $userDto, $token): void
    {
        $mensagem = $this->sendWelcomeMessage($userDto->firstName, $token);
        $this->_emailService->sendEmail($userDto->email, 'AtivoByte - Ative sua conta', $mensagem);
    }

    /**
     * Updates a user's two-factor authentication token.
     *
     * Sets a new two-factor token for the user and adjusts the expiration date to one day ahead.
     * Persists the changes in the database and maps the User entity back to a UserResponseDto.
     *
     * @param User $user The User entity whose token will be updated.
     * @param string $token The new two-factor token to be set.
     * @return UserResponseDto The updated user DTO.
     * @throws \InvalidArgumentException If the provided token is null.
     * @throws Exception If an error occurs during the token update.
     */
    private function updateToken(User $user, string $token): UserResponseDto
    {
        if ($token == null) {
            throw new \InvalidArgumentException("Token Inválido.");
        }

        try {
            $user->setTwoFactorToken($token);
            $user->setTwoFactorExpiresAt((new \DateTimeImmutable())
                ->modify('+1 days'));
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();
            $userDto = $this->_mapperResponseService->mapUserToDto($user);

            return $userDto;
        } catch (Exception $e) {
            throw new Exception("Erro ao atualizar token: " . $e->getMessage());
        }
    }

    /**
     * Updates a user's information.
     *
     * Checks if the $userDto is null and throws an exception with an appropriate message.
     * Attempts to find the user with the provided ID in the $userDto.
     * If the user is not found, throws an exception with an appropriate message.
     * Updates the user's information with the data provided in the $userDto.
     * Persists the changes in the database and flushes the changes.
     * Returns an operation result with a success message.
     * In case of an error, throws an exception with the detailed message.
     *
     * @param UserCreateDto $userDto The DTO of the user to be updated.
     * @return ResultOperation The operation result with the message and updated user data.
     * @throws Exception If an error occurs during the user update.
     */
    public function updateUser(string $id, UserCreateDto $userDto): ResultOperation
    {
        if (empty($id) || $id === null)
            return new ResultOperation(false, 'Identificador não pode ser null.');
        if ($userDto == null || $userDto->isEmpty())
            return new ResultOperation(false, 'Usuário não pode ser null.');

        try {
            $userExist = $this->getEntityManager()->getRepository(User::class)->findOneBy(['id' => $id]);

            if ($userExist == null) {
                return new ResultOperation(false, 'Usuário não encontrado');
            }

            $userExist->setEmail($userDto->email);
            $userExist->getPassword();
            $userExist->setFirstname($userDto->firstName);
            $userExist->setLastname($userDto->lastName);
            $userExist->setUserName($userDto->userName);
            $userExist->setCnpjCpf($userDto->cnpjCpfRg);


            $this->getEntityManager()->persist($userExist);

            $this->getEntityManager()->flush();

            return new ResultOperation(true, 'Usuário atualizado com sucesso');
        } catch (Exception $e) {
            throw new Exception(false, 'Erro ao atualizar usuário: ' . $e->getMessage());
        }
    }
    
    /**
     * Removes a user from the database based on the provided ID.
     *
     * Checks if the ID is null and returns a failure if that is the case.
     * If the user is not found, returns a failure message.
     * Otherwise, removes the user and saves the changes,
     * returning a success message.
     *
     * @param string $id The ID of the user to be removed.
     * @return ResultOperation The operation result with the success or error message.
     * @throws Exception If an error occurs while trying to remove the user.
     */
    public function deleteUserById(string $id): ResultOperation
    {
        if (empty($id)) {
            return new ResultOperation(false, 'Identificador não pode ser null.');
        }

        try {
            $user = $this->getEntityManager()->getRepository(User::class)->find($id);

            if ($user == null) {
                return new ResultOperation(false, 'Usuário não encontrado');
            }

            $this->getEntityManager()->remove($user);
            $this->getEntityManager()->flush();

            return new ResultOperation(true, 'Usuário deletado com sucesso');
        } catch (Exception $e) {
            return new ResultOperation(false, 'Erro ao deletar usuário: ' . $e->getMessage());
        }
    }


    public function findUserJwt(string $token): ResultOperation
    {
        if (empty($token)) return new ResultOperation(false, 'Token nao pode ser null');

        try {

            $decodedToken  = $this->_twoFactorAuthService->verifyToken($token);

            if ($decodedToken  == null) return new ResultOperation(false, 'Token nao encontrado');

            $user = $this->findUserByEmailOrUsername($decodedToken->email);

            if ($user == null) return new ResultOperation(false, 'Usuário nao encontrado');

            $userDto = $this->_mapperResponseService->mapUserToDto($user);
            return new ResultOperation(true, 'Usuário encontrado com sucesso', [$userDto]);
        } catch (Exception $e) {
            return new ResultOperation(false, 'Erro ao buscar usuario: ' . $e->getMessage());
        }
    }


    /**
     * Searches for a user by ID.
     *
     * Checks if the ID is null and returns null if that is the case.
     * If the user is not found, returns null.
     * Otherwise, returns the found user.
     *
     * @param string $id The ID of the user to be searched.
     * @return ResultOperation|null The found user or null if not found.
     * @throws Exception If an error occurs while trying to search for the user.
     */
    public function findUserById(string $id): ?ResultOperation
    {
        if (empty($id))
            return new ResultOperation(false, 'Identificador não pode ser null.');

        try {
            $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(
                ['id' => $id]
            );

            if ($user === null)
                return new ResultOperation(false, 'Usuário não encontrado.');

            $userDto = $this->_mapperResponseService->mapUserToDto($user);

            return new ResultOperation(true, 'Usuário encontrado com sucesso.', [$userDto]);
        } catch (Exception $e) {
            return new ResultOperation(false, 'Erro ao buscar usuário pelo id: ' . $e->getMessage());
        }
    }

    /**
     * Searches for a user by email.
     *
     * Checks if the email is null and returns a failure if that is the case.
     * If the user is not found, returns a failure message.
     * Otherwise, returns a ResultOperation with the found user.
     *
     * @param string $email The email of the user to be searched.
     * @return ResultOperation The ResultOperation with the found user or an error message.
     * @throws Exception If an error occurs while trying to search for the user by email.
     */
    public function findUserByEmail(string $email): ?ResultOperation
    {
        if (
            $email == null || empty($email) ||
            !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {

            return new ResultOperation(false, 'Email inválido');
        }

        try {

            $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(
                ['email' => $email]
            );

            if ($user === null) {

                return new ResultOperation(false, 'Usuário não existe');
            }
            $userDto = $this->_mapperResponseService->mapUserToDto($user);
            return new ResultOperation(true, 'Usuário encontrado com sucesso!', [$userDto]);
        } catch (Exception $e) {

            return new ResultOperation(false, 'Erro ao buscar usuário pelo email: ' . $e->getMessage());
        }
    }


    public function findUserByUserName(string $userName): ?ResultOperation
    {
        if (empty($userName)) {

            return new ResultOperation(false, 'Nome do usuário null.');
        }

        try {

            $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(
                ['userName' => $userName]
            );

            if ($user == null) {

                return new ResultOperation(false, 'Usuário não existe.', [$user]);
            }

            return new ResultOperation(true, 'Usuário encontrado com sucesso.', [$user]);
        } catch (Exception $e) {

            return new ResultOperation(false, 'Erro ao buscar usuário pelo userName: ' . $e->getMessage());
        }
    }

    public function findUserByDocument(string $cnpjCpfRg): ?ResultOperation
    {
        if (empty($cnpjCpfRg)) {

            return new ResultOperation(false, 'Cpf ou cnpj do usuário null.');
        }

        try {

            $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['cnpjCpfRg' => $cnpjCpfRg]);

            if ($user == null) {

                return new ResultOperation(false, 'Usuário nao encontrado.', [$user]);
            }

            return new ResultOperation(true, 'Usuário encontrado com sucesso.', [$user]);
        } catch (Exception $e) {

            return new ResultOperation(false, 'Erro ao buscar usuário pelo cpf ou cnpj.' . $e->getMessage());
        }
    }

    /**
     * Confirms the password reset for a user with the given token and new password.
     *
     * Validates the provided token and password, checks if the token is valid and not expired,
     * and updates the user's password if all checks pass. Resets the password reset token and its expiration date.
     *
     * @param string $token The reset password token.
     * @param string $password The new password to be set.
     * @return ResultOperation An operation result indicating the success or failure of the password reset.
     * @throws Exception If an error occurs during the password reset process.
     */
    public function confirmPasswordReset(string $token, string $password): ResultOperation
    {
        if (empty($token) || empty($password))
            return new ResultOperation(false, 'Token e senha não podem ser vazias.');

        try {
            $user = $this->getEntityManager()->getRepository(
                User::class
            )->findOneBy(['resetPasswordToken' => $token]);

            if ($user == null)
                return new ResultOperation(false, 'Token inválido.');


            if ($this->verifyTokenExpiredRecoveryAccount($user) == false)
                return new ResultOperation(false, 'Token expírado.');

            $user->resetPassword($password);
            $user->setResetPasswordToken(null);
            $user->setResetPasswordTokenExpiresAt(null);

            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();

            return new ResultOperation(true, 'Senha redefinida com sucesso.');
        } catch (Exception $e) {
            return new ResultOperation(false, 'Erro ao redefinir senha: ' . $e->getMessage());
        }
    }

    private function returnDoctrineFindParams(string $property, string $value): DoctrineFindParams
    {
        return new DoctrineFindParams(
            $property,
            $value,
            TypeEntitiesEnum::USER
        );
    }
}

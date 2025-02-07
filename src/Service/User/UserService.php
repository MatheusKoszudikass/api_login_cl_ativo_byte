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
     * Verifica se o token de recuperação de senha existe no banco de dados e
     * não expirou.
     *
     * Verifica se o token de recuperação de senha existe no banco de dados,
     * filtrando por token e verificando se a data de expiração é maior que a
     * data atual.
     *
     * Se o token for vazio, retorna false.
     *
     * @param string $token O token de recuperação de senha a ser verificado.
     * @return bool True se o token existe e não expirou, false caso contrário.
     * @throws Exception Se ocorrer um erro ao verificar o token no banco de dados.
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
     * Verifica se o token de recuperação de senha expirou.
     *
     * Verifica se o token de recuperação de senha do usuário está nulo e
     * se o token expirou com base no método do objeto User.
     *
     * @param User $user O objeto User a ser verificado
     * @return bool True se o token expirou, false caso contrário
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
     * Procura um usuário por email ou nome de usuário.
     *
     * Se o $identifier for um email, procura um usuário com o email fornecido.
     * Caso não encontre, procura um usuário com o nome de usuário fornecido.
     *
     * @param string $identifier O email ou nome de usuário a ser procurado
     * @return User|null O usuário encontrado ou null caso não encontre
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
     * Envia um email para o usuário com um token de ativação da conta.
     *
     * @param UserResponseDto $userDto O DTO do usuário que precisa ativar sua conta
     */
    private function sendTwoFactorActivationEmail(UserResponseDto $userDto, $token): void
    {
        $mensagem = $this->sendWelcomeMessage($userDto->firstName, $token);
        $this->_emailService->sendEmail($userDto->email, 'AtivoByte - Ative sua conta', $mensagem);
    }

    /**
     * Atualiza o token de autenticação de dois fatores de um usuário.
     *
     * Define um novo token de dois fatores para o usuário e ajusta a data de expiração para um dia à frente.
     * Persiste as alterações no banco de dados e mapeia a entidade User de volta para um UserResponseDto.
     *
     * @param User $user A entidade User cujo token será atualizado.
     * @param string $token O novo token de dois fatores a ser definido.
     * @return UserResponseDto O DTO do usuário atualizado.
     * @throws \InvalidArgumentException Se o token fornecido for nulo.
     * @throws Exception Em caso de erro durante a atualização do token.
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
     * Atualiza as informações de um usuário.
     *
     * Verifica se o $userDto é nulo e lança uma exceção com uma mensagem apropriada.
     * Tenta encontrar o usuário com o ID fornecido no $userDto.
     * Se o usuário não for encontrado, lança uma exceção com uma mensagem apropriada.
     * Atualiza as informações do usuário com os dados fornecidos no $userDto.
     * Persiste as alterações no banco de dados e flusha as alterações.
     * Retorna uma operação de resultado com uma mensagem de sucesso.
     * Em caso de erro, lança uma exceção com a mensagem detalhada.
     *
     * @param UserCreateDto $userDto O DTO do usuário a ser atualizado.
     * @return ResultOperation A operação de resultado com a mensagem e os dados do usuário atualizado.
     * @throws Exception Em caso de erro durante a atualização do usuário.
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
     * Remove um usuário do banco de dados com base no ID fornecido.
     *
     * Verifica se o ID é nulo e retorna uma falha se for o caso.
     * Caso o usuário não seja encontrado, retorna uma mensagem de falha.
     * Caso contrário, remove o usuário e salva as alterações,
     * retornando uma mensagem de sucesso.
     *
     * @param string $id O ID do usuário a ser removido.
     * @return ResultOperation A operação de resultado com a mensagem de sucesso ou erro.
     * @throws Exception Em caso de erro ao tentar remover o usuário.
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
     * Busca um usuário por ID.
     *
     * Verifica se o ID é nulo e retorna null se for o caso.
     * Caso o usuário não seja encontrado, retorna null.
     * Caso contrário, retorna o usuário encontrado.
     *
     * @param string $id O ID do usuário a ser buscado.
     * @return ResultOperation|null O usuário encontrado ou null caso não encontre.
     * @throws Exception Em caso de erro ao tentar buscar o usuário.
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
     * Busca um usuário pelo email.
     *
     * Verifica se o email é nulo e retorna uma falha se for o caso.
     * Caso o usuário não seja encontrado, retorna uma mensagem de falha.
     * Caso contrário, retorna um ResultOperation com o usuário encontrado.
     *
     * @param string $email O email do usuário a ser buscado.
     * @return ResultOperation O ResultOperation com o usuário encontrado ou uma mensagem de erro.
     * @throws Exception Em caso de erro ao tentar buscar o usuário pelo email.
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

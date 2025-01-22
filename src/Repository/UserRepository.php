<?php

namespace App\Repository;

use App\Dto\Create\UserCreateDto;
use App\Dto\Response\UserResponseDto;
use App\Entity\User;
use App\Entity\Role;
use App\Interface\RoleRepositoryInterface;
use App\Interface\UserRepositoryInterface;
use App\Result\ResultOperation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use App\Service\MapperServiceCreate;
use App\Service\EmailService;
use App\Service\MapperServiceResponse;
use App\Service\TwoFactorAuthService;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface, PasswordUpgraderInterface
{
    private RoleRepositoryInterface $_roleRepository;
    private MapperServiceCreate $_mapperServiceCreate;
    private MapperServiceResponse $_mapperServiceResponse;
    private EmailService $_mailer;
    private TwoFactorAuthService $_twoFactorAuthService;

    public function __construct(
        ManagerRegistry $registry,
        RoleRepositoryInterface $roleRepository,
        MapperServiceCreate $mapperServiceCreate,
        MapperServiceResponse $mapperServiceResponse,
        EmailService $mailer,
        TwoFactorAuthService $twoFactorAuthService
    ) {
        parent::__construct($registry, User::class);
        $this->_mapperServiceCreate = $mapperServiceCreate;
        $this->_mapperServiceResponse = $mapperServiceResponse;
        $this->_roleRepository = $roleRepository;
        $this->_mailer = $mailer;
        $this->_twoFactorAuthService = $twoFactorAuthService;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        // if (!$user instanceof User) {
        //     throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        // }

        // $user->getPassword();
        // $this->getEntityManager()->persist($user);
        // $this->getEntityManager()->flush();
    }

    /**
     * Cria um novo usuário e suas respectivas associações com roles.
     * 
     * Recebe um objeto UserCreateDto e o mapeia para uma entidade User,
     * persistindo-a no banco de dados.
     * 
     * Itera sobre as roles fornecidas no DTO e verifica se elas existem.
     * Se uma role não existir, cria uma nova com o nome fornecido.
     * 
     * Salva as alterações no banco de dados e mapeia a entidade User de volta
     * para um DTO de resposta.
     * 
     * Gera uma mensagem de boas-vindas e envia um email para o usuário.
     * 
     * Retorna uma operação de resultado com uma mensagem e os dados do usuário
     * criado.
     * 
     * @param UserCreateDto $userDto O objeto UserCreateDto a ser mapeado para uma entidade User.
     * @return ResultOperation A operação de resultado com a mensagem e os dados do usuário criado.
     * @throws Exception Em caso de erro, lança uma exceção com a mensagem detalhada.
     */
    public function createUser(UserCreateDto $userDto): ResultOperation
    {
        if ($userDto == null || $userDto->isEmpty()) {
            return new ResultOperation(false, 'Usuário não pode ser nulo');
        }

        try {

            $resultVerify = $this->verifyIfUserHasExist($userDto);

            if ($resultVerify instanceof ResultOperation && $resultVerify->isSuccess() == false) {
                return $resultVerify;
            }

            $user = $this->checkIfUserForCreationExists($userDto);

            $this->setUserTwoFactorExpiration($user);

            $this->saveUser($user);

            $this->sendWelcomeEmail($user);

            return new ResultOperation(
                true,
                'Usuário criado com sucesso. Verifique sua caixa de email para ativação da conta.'
            );
        } catch (Exception $e) {
            // $this->getEntityManager()->rollback();
            throw new Exception("Erro ao criar usuário: " . $e->getMessage());
        }
    }


    /**
     * Verifica se o usuário já existe com base no email ou no username.
     * 
     * Busca um usuário com o email e o username fornecidos no DTO.
     * Caso um dos dois exista, retorna uma ResultOperation com um erro.
     * Caso contrário, retorna uma ResultOperation com um sucesso.
     * 
     * @param UserCreateDto $userDto O DTO do usuário a ser verificado.
     * @return ResultOperation A ResultOperation com o resultado da verificação.
     */
    private function verifyIfUserHasExist(UserCreateDto $userDto): Object
    {
        $result = $this->findUserByEmailOrUsername($userDto->email);

        if ($result != null) return new ResultOperation(false, 'Usuário já existe');

        $result = $this->findUserByEmailOrUsername($userDto->userName);

        if ($result != null) return new ResultOperation(false, 'Usuário já existe');

        return new ResultOperation(true, 'Usuário pode ser criado');
    }

    /*************  ✨ Codeium Command ⭐  *************/
    /**
     * Maps a UserCreateDto to a User entity and sets a two-factor token.
     * 
     * Generates a two-factor authentication token for the user,
     * maps the UserCreateDto to a User entity, and assigns the token
     * to the user. Verifies and adds roles to the user based on the
     * provided DTO.
     * 
     * @param UserCreateDto $userDto The DTO containing user information.
     * @return User The user entity with the two-factor token set.
     */

    /******  255fd08e-2a6d-417b-b1fc-bcef9aefdeed  *******/
    private function checkIfUserForCreationExists(UserCreateDto $userDto): Object
    {
        $token = $this->_twoFactorAuthService->generateToken();

        $user = $this->_mapperServiceCreate->mapUser($userDto);

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
        $user->setTowoFactorExpiresAt(
            (new \DateTimeImmutable('now'))->modify('+1 days')
        );
    }

    private function saveUser($user): void
    {
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    private function sendWelcomeEmail($user): void
    {
        $mensagem = $this->sendWelcomeMessage(
            $user->getfullName(),
            $user->getTwoFactorToken()
        );

        $this->_mailer->sendEmail(
            $user->getEmail(),
            'AtivoByte - Cadastrado',
            $mensagem
        );
    }

    /**
     * Verifica se o token de autenticação de dois fatores é válido e
     * habilita a autenticação de dois fatores para o usuário.
     *
     * Se o token for inválido, retorna uma falha.
     * Se o token for válido, mas tiver expirado, gera um novo token e
     * envia um novo email com o token atualizado, retornando uma mensagem
     * de falha.
     * Se o token for válido e não tiver expirado, desabilita o token e
     * habilita a autenticação de dois fatores, retornando sucesso.
     *
     * @param string $token Token de autenticação de dois fatores
     * @return ResultOperation Operação de resultado com mensagem e status
     * @throws Exception Exceção em caso de erro
     */
    public function enableTwoFactorAuth(string $token): ResultOperation
    {
        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(
            ['twoFactorToken' => $token]
        );

        if (empty($user)) {
            // Retorna uma falha caso o token não corresponda a nenhum usuário
            return new ResultOperation(false, 'Token inválido');
        }

        try {
            // Verifica se o token expirou com base no método do objeto User
            if ($user->verifyTwoFactorExpiresAt(new \DateTimeImmutable())) {

                // Gera um novo token caso o atual tenha expirado
                $token = $this->_twoFactorAuthService->generateToken();

                // Atualiza o token no usuário e mapeia para um DTO
                $userDto = $this->updateToken($user, $token);

                // Envia um novo email com o token atualizado
                $mensagem = $this->sentMessageCreateUser($userDto->firstName . ' ' . $userDto->lastName, $token);
                $this->_mailer->sendEmail($userDto->email, 'AtivoByte - Cadastrado', $mensagem);

                // Retorna uma mensagem de falha indicando que o token expirou, mas um novo foi enviado
                return new ResultOperation(false, 'Token expirado, verifique seu e-mail');
            }

            // Caso o token seja válido, desabilita o token e habilita a autenticação de dois fatores
            $user->setTwoFactorToken('');
            $user->setTwoFactorEnabled(true);

            // Persiste as alterações no banco de dados
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();

            // Retorna sucesso
            return new ResultOperation(true, 'Token verificado com sucesso');
        } catch (Exception $e) {
            // Lança uma exceção personalizada para erros durante o processo
            throw new Exception("Erro ao verificar token: " . $e->getMessage());
        }
    }


    /**
     * Envia uma mensagem de boas-vindas para o usuário com um link para ativar sua conta.
     *
     * @param string $firstName Nome do usuário
     * @param string $token Token de autenticação de dois fatores
     * @return string Mensagem de boas-vindas em formato HTML
     */
    /******  92bcd828-6415-433a-be50-4776ed796da6  *******/
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

    /**
     * Envia uma mensagem de recuperação de senha para o usuário com um link para redefinir sua senha.
     *
     * @param string $firstName Nome do usuário
     * @param string $token Token de autenticação de dois fatores
     * @return string Mensagem de recuperação de senha em formato HTML
     */
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

    /**
     * Valida o usuário e verifica se a conta está ativa.
     *
     * Busca um usuário com base no email e senha fornecidos no DTO.
     * Caso o usuário não exista ou a senha esteja incorreta, retorna
     * uma operação de resultado com um erro.
     * Caso o usuário exista e a senha esteja correta, verifica se a
     * conta está ativa. Se a conta não estiver ativa, gera um novo
     * token de autenticação de dois fatores, atualiza o token no
     * usuário e mapeia para um DTO, enviando um email com o token
     * atualizado.
     * Se a conta estiver ativa, retorna uma operação de resultado
     * com sucesso.
     * Em caso de erro, lança uma exceção personalizada com uma
     * mensagem de erro.
     *
     * @param UserCreateDto $userDto DTO com os dados do usuário
     * @return ResultOperation Operação de resultado com o resultado da
     * validação
     */
    public function validateUser(UserCreateDto $userDto): ResultOperation
    {
        try {

            $user = $this->findUserByEmailOrUsername($userDto->email);

            if (!$user || !$user->authenticate($userDto->password)) {
                return new ResultOperation(false, 'Email ou senha incorretos');
            }

            $userDto = $this->_mapperServiceResponse->mapUserToDto($user);

            if (!$user->isTwoFactorEnabled()) {
                if ($user->verifyTwoFactorExpiresAt(
                    new \DateTimeImmutable('now')
                ) == true) {
                    // Gera um novo token caso o atual tenha expirado
                    $token = $this->_twoFactorAuthService->generateToken();
                    // Atualiza o token no usuário e mapeia para um DTO
                    $userDto = $this->updateToken($user, $token);
                    $this->sendTwoFactorActivationEmail($userDto, $token);
                }
                return new ResultOperation(false, 'Conta não ativada, verifique o email cadastrado!');
            }

            return new ResultOperation(true, 'Usuário valídado com sucesso!');
        } catch (Exception $e) {
            return new ResultOperation(false, "Erro na validação do usuário: " . $e->getMessage());
        }
    }

    /**
     * Verifica se o usuário existe com base no email ou no username.
     * 
     * Busca um usuário com o email e o username fornecidos no parâmetro $identifier.
     * Caso um dos dois exista, retorna uma ResultOperation com um erro.
     * Caso contrário, retorna uma ResultOperation com um sucesso.
     * 
     * @param string $identifier email ou nome de usuário do usuário a ser verificado.
     * @return ResultOperation A ResultOperation com o resultado da verificação.
     * @throws Exception Caso ocorra um erro ao buscar o usuário.
     */
    public function userExists(string $identifier): ResultOperation
    {
        if ($identifier == null || $identifier === '') return new ResultOperation(false, 'Identificador null.');
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

    /**
     * Verifica se o token de autenticação de dois fatores é válido e
     * se o usuário está desativado.
     *
     * Se o token for inválido, gera um novo token e envia um email com
     * o token atualizado.
     * Se o token for válido e o usuário estiver desativado, retorna true.
     *
     * @param User $user O objeto User a ser verificado.
     * @param string $email O email do usuário.
     * @return bool True se o token for inválido ou o usuário estiver desativado, false caso contrário.
     */
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

                $this->_mailer->sendEmail(
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

        try 
        {
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
        $this->_mailer->sendEmail($userDto->email, 'AtivoByte - Ative sua conta', $mensagem);
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
            $userDto = $this->_mapperServiceResponse->mapUserToDto($user);

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

            $userDto = $this->_mapperServiceResponse->mapUserToDto($user);
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

            $userDto = $this->_mapperServiceResponse->mapUserToDto($user);

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
            $userDto = $this->_mapperServiceResponse->mapUserToDto($user);
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
}

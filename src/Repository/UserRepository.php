<?php

namespace App\Repository;

use App\Dto\Create\RoleCreateDto;
use App\Dto\Create\UserCreateDto;
use App\Dto\Response\RoleResponseDto;
use App\Dto\Response\UserResponseDto;
use App\Entity\User;
use App\Repository\UnsupportedUserException;
use App\Interface\UserRepositoryInterface;
use App\Result\ResultOperation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Firebase\JWT\JWT;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use App\Service\MapperServiceCreate;
use App\Service\EmailService;
use App\Service\MapperServiceResponse;
use App\Service\TwoFactorAuthService;
use Symfony\Component\Validator\Tests\Fixtures\ToString;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface, PasswordUpgraderInterface
{
    private RoleRepository $_roleRepository;
    private MapperServiceCreate $_mapperServiceCreate;
    private MapperServiceResponse $_mapperServiceResponse;
    private EmailService $_mailer;
    private TwoFactorAuthService $_twoFactorAuthService;

    public function __construct(
        ManagerRegistry $registry,
        RoleRepository $roleRepository,
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
        // Verifica se o objeto UserCreateDto fornecido é nulo
        if ($userDto === null) {
            // Retorna uma operação de falha com uma mensagem apropriada
            return new ResultOperation(false, 'Usuário não pode ser nulo');
        }

        try {

            // Verifica se o usuário já está cadastrado
              $result = $this->findUserByEmail($userDto->email);
              $result = $this->findUserByUserName($userDto->userName);

              if($result->isSuccess() == true)return new ResultOperation(false, 'Usuário já existe');
              
            // Gera um token de autenticação de dois fatores e o atribui ao DTO
            $token = $this->_twoFactorAuthService->generateToken();
            $userDto->token = $token;

            // Mapeia o DTO para uma entidade User utilizando o serviço de mapeamento
            $user = $this->_mapperServiceCreate->mapUser($userDto);

            // Itera sobre as roles fornecidas no DTO para verificar e associar ao usuário
            foreach ($userDto->roles as $roleExistVerify) {
                
                // Procura a role pelo ID
                $role = $this->_roleRepository->findRoleById($roleExistVerify['id']);

                if ($role->isSuccess() == false) {
                    // Se não encontrar pelo ID, tenta buscar pelo nome
                    $role = $this->_roleRepository->findRoleByName($roleExistVerify['name']);
                }

                if ($role->isSuccess() == false) {
                    // Se ainda não encontrou, cria uma nova role com o nome fornecido
                    $roleCreateDto = new RoleCreateDto();
                    $roleCreateDto->name = $roleExistVerify['name'];
                    $roleCreateDto->description = $roleExistVerify['description'];

                   $role = $this->_roleRepository->createRole($roleCreateDto);
                }

                $user->createRole($this->_mapperServiceResponse->mapRole($role->getData()[0]));
            }

            // Persiste a entidade User (ainda não salva no banco até o flush)
            $this->getEntityManager()->persist($user);

            // Salva as alterações no banco de dados, incluindo o usuário e suas associações
            $this->getEntityManager()->flush();

            // Mapeia a entidade User de volta para um DTO de resposta
            $userDto = $this->_mapperServiceResponse->mapUserToDto($user);

            // Gera a mensagem de boas-vindas para o usuário
            $mensagem = $this->sendWelcomeMessage($userDto->firstName, $userDto->token);

            // Envia o email de confirmação/cadastro para o usuário
            $this->_mailer->sendEmail($userDto->email, 'AtivoByte - Cadastrado', $mensagem);

            // Retorna uma operação de sucesso com uma mensagem e os dados do usuário criado
            return new ResultOperation(true, 'Usuário criado com sucesso. Verifique sua caixa de email para ativação da conta.');

        } catch (Exception $e) {
            // Lança uma exceção em caso de erro, incluindo a mensagem detalhada
            throw new Exception("Erro ao criar usuário: " . $e->getMessage());
        }
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
        // Busca o usuário no banco de dados com base no token de autenticação de dois fatores
        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(
            ['twoFactorToken' => $token]);

        if ($user == null) {
            // Retorna uma falha caso o token não corresponda a nenhum usuário
            return new ResultOperation(false, 'Token inválido');
        }

        try {
            // Verifica se o token expirou com base no método do objeto User
            if ($user->verifyTwoFactorExpiresAt($dateTime = new \DateTimeImmutable())) {

                // Gera um novo token caso o atual tenha expirado
                $token = $this->_twoFactorAuthService->generateToken();

                // Atualiza o token no usuário e mapeia para um DTO
                $userDto = $this->updateToken($user, $token);

                // Envia um novo email com o token atualizado
                $mensagem = $this->sentMessageCreateUser($userDto->firstName.' '.$userDto->lastName, $userDto->token);
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
        } catch (\Exception $e) {
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
        return "
        <p>Seja bem-vindo ao <strong>AtivoByte</strong>, {$firstName}!</p>
        <p>Estamos felizes em tê-lo conosco e esperamos que tenha uma ótima experiência em nossa plataforma.</p>
        <p>Para completar o seu cadastro e ativar sua conta, por favor, clique no link abaixo:</p>
        <p><a href='https://www.cliente.ativobyte.com/active-user?token={$token}'>Ativar minha conta</a></p>
        <p>Se você não solicitou esse cadastro, por favor, ignore este e-mail.</p>
        <p>Atenciosamente,<br>Equipe Ativo Byte</p>
      ";
    }

    public function sendPasswordRecoveryMessage(string $firstName, string $token): string
{
    return "
    <p>Olá, {$firstName},</p>
    <p>Recebemos uma solicitação para redefinir sua senha na plataforma <strong>AtivoByte</strong>.</p>
    <p>Se foi você quem solicitou, clique no link abaixo para redefinir sua senha:</p>
    <p><a href='https://www.ativobyte.com/api/user/reset-password?token={$token}'>Redefinir minha senha</a></p>
    <p>Este link é válido por 24 horas. Após esse período, você precisará solicitar um novo link.</p>
    <p>Se você não solicitou a recuperação de senha, por favor, ignore este e-mail. Sua conta permanecerá segura.</p>
    <p>Atenciosamente,<br>Equipe Ativo Byte</p>
    ";
}

    /**
     * Valida um usuário e retorna um DTO com os dados do usuário caso esteja correto.
     *
     * Verifica se o email ou nome de usuário existe e se a senha é válida.
     * Caso o usuário esteja desativado, envia um email com um link para ativar.
     * Caso contrário, retorna um DTO com os dados do usuário.
     *
     * @param UserCreateDto $userDto DTO com os dados do usuário a ser validado
     * @return UserResponseDto DTO com os dados do usuário caso esteja correto
     * @throws Exception Caso o email ou senha estejam incorretos ou caso o usuário esteja desativado
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
                // Gera um novo token caso o atual tenha expirado
                $token = $this->_twoFactorAuthService->generateToken();

                // Atualiza o token no usuário e mapeia para um DTO
                $userDto = $this->updateToken($user, $token);
                $this->sendTwoFactorActivationEmail($userDto);
                return new ResultOperation(false, 'Conta não ativada, verifique o email cadastrado!');
            }

            return new ResultOperation(true, 'Usuário valídado com sucesso!');
        } catch (Exception $e) {
           return new ResultOperation(false, "Erro na validação do usuário: " . $e->getMessage());
        }
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
    private function findUserByEmailOrUsername(string $identifier): ?User
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
    private function sendTwoFactorActivationEmail(UserResponseDto $userDto): void
    {
        $mensagem = $this->sendWelcomeMessage($userDto->firstName, $userDto->token);
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
            $user->getTwoFactorExpiresAt($dateTime =
                (new \DateTimeImmutable())->modify('+1 days'));
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
     * @param UserResponseDto $userDto O DTO do usuário a ser atualizado.
     * @return ResultOperation A operação de resultado com a mensagem e os dados do usuário atualizado.
     * @throws Exception Em caso de erro durante a atualização do usuário.
     */
    public function updateUser(UserResponseDto $userDto): ResultOperation
    {
        if ($userDto == null) {
            return new ResultOperation(false, 'Usuário não pode ser nulo');
        }

        try {
            $userExist = $this->getEntityManager()->getRepository(User::class)->find($userDto->id);

            if ($userExist == null) {
                return new ResultOperation(false, 'Usuário não encontrado');
            }

            $userExist = new User(
                $userDto->email,
                $userExist->getPassword(),
                $userDto->firstName,
                $userDto->lastName,
                $userDto->userName,
                $userDto->cnpjCpfRg,
            );


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
        if ($id == null) {
            return new ResultOperation(false, 'Id não pode ser nulo');
        }

        try {
            $user = $this->_em->getRepository(User::class)->find($id);

            if ($user == null) {
                return new ResultOperation(false, 'Usuário não encontrado');
            }

            $this->_em->remove($user);
            $this->_em->flush();

            return new ResultOperation(true, 'Usuário deletado com sucesso');
        } catch (Exception $e) {
            return new ResultOperation(false, 'Erro ao deletar usuário: ' . $e->getMessage());
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
     * @return User|null O usuário encontrado ou null caso não encontre.
     * @throws Exception Em caso de erro ao tentar buscar o usuário.
     */
    public function findUserById(string $id): ?ResultOperation
    {
        if ($id == null) {
            return null;
        }

        try {
            $user = $this->_em->getRepository(User::class)->find($id);

            if ($user == null) {
                return null;
            }
            return $user;
        } catch (Exception $e) {
            throw new Exception('Erro ao buscar usuário pelo id: ' . $e->getMessage());
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
        if ($email == null) {

            return new ResultOperation(false, 'Email nullo');
        }

        try {

            $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(
                ['email' => $email]);

            if ($user == null) {
                
                return new ResultOperation(false, 'Usuário não existe', [$user]);
            }

            return new ResultOperation(true, 'Usuário encontrado com sucesso!', [$user]);

        } catch (Exception $e) {

           return new ResultOperation(false, 'Erro ao buscar usuário pelo email: ' . $e->getMessage());
        }
    }

    /**
     * Busca um usuário pelo nome de usuário.
     *
     * Verifica se o nome de usuário é nulo e retorna uma falha se for o caso.
     * Caso o usuário não seja encontrado, retorna uma mensagem de falha.
     * Caso contrário, retorna um ResultOperation com o usuário encontrado.
     *
     * @param string $userName O nome de usuário a ser buscado.
     * @return ResultOperation O ResultOperation com o usuário encontrado ou uma mensagem de erro.
     * @throws Exception Em caso de erro ao tentar buscar o usuário pelo nome de usuário.
     */

    public function findUserByUserName(string $userName): ?ResultOperation
    {
        if ($userName == null) {

            return new ResultOperation(false, 'Nome do usuário nulo.');
        }

        try {

            $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(
                ['userName' => $userName]);

            if ($user == null) {

                return new ResultOperation(false, 'Usuário não existe', [$user]);
            }

            return new ResultOperation(true, 'Usuário encontrado com sucesso!', [$user]);

        } catch (Exception $e) {

            return new ResultOperation(false, 'Erro ao buscar usuário pelo userName: ' . $e->getMessage());
        }
    }

    public function findUserByDocument(string $cnpjCpfRg): ?ResultOperation
    {
        if ($cnpjCpfRg == null) {

            return new ResultOperation(false, 'Cpf ou cnpj do usuário nulo.');
        }

        try {

            $user = $this->_em->getRepository(User::class)->findOneBy(['cnpjCpfRg' => $cnpjCpfRg]);

            if ($user == null) {

                return new ResultOperation(true, 'Usuário não existe', [$user]);
            }

            return new ResultOperation(true, 'Usuário encontrado com sucesso!', [$user]);

        } catch (Exception $e) {

           return new ResultOperation(false, 'Erro ao buscar usuário pelo cpf ou cnpj: ' . $e->getMessage());
        }
    }


    public function authenticateUser(string $email, string $password): ResultOperation
    {
        if ($email == null || $password == null) {

            return new ResultOperation(false, 'Usuário não pode ser nulo');
        }

        try {
            $user = $this->_em->getRepository(User::class)->findOneBy(['email' => $email]);

            if ($user == null) {

                return new ResultOperation(false, 'Usuário não encontrado');
            }

            if (!password_verify($password, $user->getPassword())) {

                return new ResultOperation(false, 'Senha inválida');
            }

            $secretKey = "c>URH{44N7CM-t'IJfvqe]@S\]ew6S\\n1N-+'H£5<2E!w)8-^p";
            $expirationTime = time() + 66000;

            $payload = [
                'user_id' => $user->getId(),
                'email' => $user->getEmail(),
                'exp' => $expirationTime
            ];

            $token = JWT::encode($payload, $secretKey, 'HS256');

            return new ResultOperation(true, 'Login realizado com sucesso', ['token', $token]);

        } catch (Exception $e) {

            throw new Exception(false, 'Erro ao buscar usuário: ' . $e->getMessage());
        }
    }

    public function initiatePasswordReset(string $email): ResultOperation
    {
        if ($email == null) {

            return new ResultOperation(false, 'Email não pode ser nulo');
        }

        try {

            $user = $this->_em->getRepository(User::class)->findOneBy(['email' => $email]);

            if ($user == null) {
                return new ResultOperation(false, 'Usuário não encontrado');
            }

            $resetToken = bin2hex(random_bytes(16));
            $expirationTime = new \DateTime('+2 hour');

            $this->sentPasswordResetEmail($user->getEmail(),  $resetToken);

            return new ResultOperation(true, 'Instruções para redefinição de senha foram enviadas para o seu e-mail');

        } catch (Exception $e) {
            
            throw new Exception(false, 'Erro ao buscar usuário: ' . $e->getMessage());
        }
    }

    public function confirmPasswordReset(string $token, string $password): ResultOperation
    {
        if ($token == null || $password == null) {
            return new ResultOperation(false, 'Token e senha não podem ser nulo');
        }

        try {
            $user = $this->_em->getRepository(User::class)->findOneBy(['passwordResetToken' => $token]);

            if ($user == null || new \DateTime() > $user->getPasswordResetExpiresAt()) {
                return new ResultOperation(false, 'Token inválido');
            }

            $hashedPassword = password_hash($password,  PASSWORD_BCRYPT);
            $user->setPassword($hashedPassword);
            $user->setPasswordResetToken(null);
            $user->setPasswordResetExpiresAt(null);

            $this->_em->persist($user);
            $this->_em->flush();

            return new ResultOperation(true, 'Senha redefinida com sucesso');
        } catch (Exception $e) {
            throw new Exception(false, 'Erro ao redefinir senha: ' . $e->getMessage());
        }
    }



    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

<?php

namespace App\Repository;

use App\Dto\Create\UserCreateDto;
use App\Dto\Response\UserResponseDto;
use App\Entity\User;
use App\Entity\Role;
use App\Interface\UserRepositoryInterface;
use App\Result\ResultOperation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Firebase\JWT\JWT;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use App\Service\MapperServiceCreate;
use App\Service\EmailService;
use App\Service\MapperServiceResponse;
use App\Service\TwoFactorAuthService;

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
        MapperServiceCreate $mapperServiceCreate,
        MapperServiceResponse $mapperServiceResponse,
        RoleRepository $roleRepository,
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
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->getPassword();
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function addUser(UserCreateDto $userDto): ResultOperation
    {
        if ($userDto === null) {
            return new ResultOperation(false, 'Usuário não pode ser nulo');
        }

        try {
            $token = $this->_twoFactorAuthService->generateToken();
            $userDto->token = $token;
            $user = $this->_mapperServiceCreate->mapUser($userDto);
            $this->getEntityManager()->persist($user);

            foreach ($userDto->roles as $roleExistVerify) {
                $role = $this->_roleRepository->findOneBy(['id' => $roleExistVerify]);

                if (!$role) {
                    $role = $this->_roleRepository->findOneBy(['name' => $roleExistVerify]);
                }

                if (!$role) {
                    $role = new Role($roleExistVerify);
                    $role->getDescription($roleExistVerify); // Define o nome da role
                    $this->getEntityManager()->persist($role);
                    $this->getEntityManager()->flush();
                }

                $user->addRole($role);
            }
            $this->getEntityManager()->flush();
            $userDto = $this->_mapperServiceResponse->mapUserToDto($user);
            $mensagem = $this->sentMessageCreateUser($userDto->firstName, $userDto->token);
            $this->_mailer->sendEmail($userDto->email, 'AtivoByte - Cadastrado', $mensagem);
            return new ResultOperation(true, 'Usuário criado com sucesso', [$user]);
        } catch (Exception $e) {

            throw new Exception("Erro ao criar usuário: " . $e->getMessage());
        }
    }

/**
 * Generates a welcome message for new users including an account activation link.
 *
 * @param UserResponseDto $userDto The data transfer object containing user information.
 * 
 * @return string The HTML message to be sent to the user's email.
 */

    public function sentMessageCreateUser(string $firstName, string $token): string
    {
        return "
        <p>Seja bem-vindo ao <strong>AtivoByte</strong>, {$firstName}!</p>
        <p>Estamos felizes em tê-lo conosco e esperamos que tenha uma ótima experiência em nossa plataforma.</p>
        <p>Para completar o seu cadastro e ativar sua conta, por favor, clique no link abaixo:</p>
        <p><a href='https://www.ativobyte.com/api/user/active-user?token={$token}'>Ativar minha conta</a></p>
        <p>Se você não solicitou esse cadastro, por favor, ignore este e-mail.</p>
        <p>Atenciosamente,<br>Equipe Ativo Byte</p>
      ";
    }


    public function verifyTwoFactorTokenAndEnable(string $token): ResultOperation
    {
        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['twoFactorToken' => $token]);
        if ($user == null) {
            return new ResultOperation(false, 'Token inválido');
        }

        try {
            if ($user->verifyTwoFactorExpiresAt($dateTime = new \DateTimeImmutable()))
             {
                $token = $this->_twoFactorAuthService->generateToken();
                
                $userDto = $this->updateToken($user, $token);
                $mensagem = $this->sentMessageCreateUser($userDto->firstName, $userDto->token);
                $this->_mailer->sendEmail($userDto->email, 'AtivoByte - Cadastrado', $mensagem);
                return new ResultOperation(false, 'Token expirado, verifique seu e-mail');
            }
            $user->setTwoFactorToken('');
            $user->setTwoFactorEnabled(true);
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();
            return new ResultOperation(true, 'Token verificado com sucesso');
        } catch (\Exception $e) {
            throw new Exception ("Erro ao verificar token: " . $e->getMessage());
        }
    }

    private function updateToken(User $user, string $token): UserResponseDto
    {
        if ($token == null) {
            throw new \InvalidArgumentException("Token Inválido.");
        }

        try 
        {
            $user->setTwoFactorToken($token);
            $user->getTwoFactorExpiresAt($dateTime = 
            (new \DateTimeImmutable())->modify('+1 days'));
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();
            $userDto = $this->_mapperServiceResponse->mapUserToDto($user);
            
           return $userDto;
        }catch(Exception $e)
        {
            throw new Exception("Erro ao atualizar token: " . $e->getMessage());
        }
    }
    
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

    public function deleteUser(string $id): ResultOperation
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
            throw new Exception(false, 'Erro ao deletar usuário: ' . $e->getMessage());
        }
    }

    public function getUserById(string $id): ?UserResponseDto
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

    public function getUserByEmail(string $email): ?UserResponseDto
    {
        if ($email == null) {
            return null;
        }

        try {
            $user = $this->_em->getRepository(User::class)->findOneBy(['email' => $email]);

            if ($user == null) {
                return null;
            }

            return $user;
        } catch (Exception $e) {
            throw new Exception('Erro ao buscar usuário pelo email: ' . $e->getMessage());
        }
    }

    public function getUserByUserName(string $userName): ?UserResponseDto
    {
        if ($userName == null) {
            return null;
        }

        try {
            $user = $this->_em->getRepository(User::class)->findOneBy(['userName' => $userName]);

            if ($user == null) {
                return null;
            }

            return $user;
        } catch (Exception $e) {
            throw new Exception('Erro ao buscar usuário pelo userName: ' . $e->getMessage());
        }
    }

    public function getUserByCnpjCpfRg(string $cnpjCpfRg): ?UserResponseDto
    {
        if ($cnpjCpfRg == null) {
            return null;
        }

        try {
            $user = $this->_em->getRepository(User::class)->findOneBy(['cnpjCpfRg' => $cnpjCpfRg]);

            if ($user == null) {
                return null;
            }

            return $user;
        } catch (Exception $e) {
            throw new Exception('Erro ao buscar usuário pelo cnpjCpfRg: ' . $e->getMessage());
        }
    }

    public function loginUser(string $email, string $password): ResultOperation
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

    public function requestPasswordReset(string $email): ResultOperation
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

    public function resetPassword(string $token, string $password): ResultOperation
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

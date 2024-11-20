<?php

namespace App\Repository;

use App\Entity\Login;
use App\Entity\User;
use App\Dto\LoginDto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Interface\LoginRepositoryInterface;
use App\Interface\UserRepositoryInterface;
use App\Result\ResultOperation;
use App\Service\TwoFactorAuthService;
use App\Interface\UserReppositoryInterface;
use Exception;
use App\Service\MapperService;
use App\Service\MapperServiceResponse;
use App\Service\EmailService;

/**
 * @extends ServiceEntityRepository<Login>
 */
class LoginRepository extends ServiceEntityRepository implements LoginRepositoryInterface
{
    private MapperService $_mapperService;
    private TwoFactorAuthService $_twoFactorAuthService;
    private MapperServiceResponse $_mapperServiceResponse;
    private EmailService $_mailer;
    private UserRepositoryInterface $_userRepository;

    public function __construct(ManagerRegistry $registry, MapperService $mapperService,
    TwoFactorAuthService $twoFactorAuthService, MapperServiceResponse $mapperServiceResponse
    , EmailService $mailer, UserRepositoryInterface $userRepository)
    {
        parent::__construct($registry, Login::class);
        $this->_mapperService = $mapperService;
        $this->_twoFactorAuthService = $twoFactorAuthService;
        $this->_mapperServiceResponse = $mapperServiceResponse;
        $this->_mailer = $mailer;
        $this->_userRepository = $userRepository;
    }

    public function addLogin(LoginDto $loginDto): ResultOperation
    {
        if($loginDto == null)
        {
            return new ResultOperation(false, 'Login não pode ser nulo');
        }

        try
        {
            $user = $this->validadeLogin($loginDto);
    
             $userData = [
                'id' => $user->getId(),
                'email' => $loginDto->email_userName,
                'ip' => $loginDto->lastLoginIp	
             ];

            $token =  $this->_twoFactorAuthService->generateTokenJwt($userData);
            $loginDto->password = $user->getPassword();
            $login = $this->_mapperService->mapLogin($loginDto);
            $this->getEntityManager()->persist($login);
            $this->getEntityManager()->flush();
            return new ResultOperation(true, 'Login realizado com sucesso', [$token]);
        }catch(Exception $e)
        {
             return new ResultOperation(false, 'Erro login: ' . $e->getMessage());
        }
    }

    private function validadeLogin(LoginDto $loginDto): ?User
    {
        $user = $this->getEntityManager()->getRepository(User::class)
        ->findOneBy(['email' => $loginDto->email_userName]);

        if(!$user)
        {
            $user = $this->getEntityManager()->getRepository(User::class)
            ->findOneBy(['userName' => $loginDto->email_userName]);

            if(!$user)
            {
               return throw new  Exception('email ou senha incorretos');
            }
        }

         if(!$user->authenticate($loginDto->password)) 
         {
            return throw new Exception('email ou senha incorretos');
         }

         if(!$user->isTwoFactorEnabled())
         {
            $userDto = $this->_mapperServiceResponse->mapUserToDto($user);
            $mensagem = $this->_userRepository->sentMessageCreateUser($userDto->firstName, $userDto->token);
            $this->_mailer->sendEmail($userDto->email, 'AtivoByte - Cadastrado', $mensagem);
             return throw new Exception('Conta não ativada, verifique o email cadastrado!');
         }

         return  $user;     
    }


    public function validadteTokenJwt(string $token): ResultOperation
    {
        try
        {
             $token = $this->_twoFactorAuthService->verifyToken($token);
            return new ResultOperation(true, 'Token autenticado com sucesso!', [$token]);

        }catch(Exception $e)
        {
            return new ResultOperation(false, 'Erro ao validar token:' . $e->getMessage());
        }
    }

    public function updateLogin(LoginDto $login): ResultOperation
    {
        try
        {
            $login = $this->_mapperService->mapLogin($login);
            $this->_em->persist($login);
            $this->_em->flush();
            return new ResultOperation(true, 'Login atualizado com sucesso');
        }catch(Exception $e)
        {
            return new ResultOperation(false, $e->getMessage());
        }
    }
}

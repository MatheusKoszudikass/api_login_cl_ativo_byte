<?php

namespace App\Repository;

use App\Entity\Login;
use App\Dto\Create\UserCreateDto;
use App\Dto\LoginDto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Interface\LoginRepositoryInterface;
use App\Interface\UserRepositoryInterface;
use App\Result\ResultOperation;
use App\Service\TwoFactorAuthService;
use Exception;
use App\Service\MapperService;
use App\Service\EmailService;
use App\Service\MapperServiceResponse;

/**
 * @extends ServiceEntityRepository<Login>
 */
class LoginRepository extends ServiceEntityRepository implements LoginRepositoryInterface
{
    private MapperService $_mapperService;
    private EmailService $_mailer;
    private TwoFactorAuthService $_twoFactorAuthService;
    private UserRepositoryInterface $_userRepository;

    private MapperServiceResponse $_mapperServiceResponse;

    public function __construct(
        ManagerRegistry $registry,
        MapperService $mapperService,
        EmailService $mailer,
        TwoFactorAuthService $twoFactorAuthService,
        UserRepositoryInterface $userRepository,
        MapperServiceResponse $mapperServiceResponse
    ) {
        parent::__construct($registry, Login::class);
        $this->_mapperService = $mapperService;
        $this->_mailer = $mailer;
        $this->_twoFactorAuthService = $twoFactorAuthService;
        $this->_userRepository = $userRepository;
        $this->_mapperServiceResponse = $mapperServiceResponse;
    }

    /**
     * Verifica se o Login esta correto e retorna um ResultOperation com um token JWT.
     * 
     * Verifica se o email ou nome de usuário existe e se a senha  e  válida.
     * Caso o usuário esteja desativado, envia um email com um link para ativar.
     * Caso contrário, retorna um ResultOperation com um token JWT.
     * 
     * @param LoginDto $loginDto DTO com os dados do login
     * @return ResultOperation ResultOperation com um token JWT
     * @throws Exception Caso o email ou senha estejam incorretos ou caso o usu rio esteja desativado
     */
    public function Login(LoginDto $loginDto): ResultOperation
    {
        if ($loginDto == null) {
            return new ResultOperation(false, 'Login não pode ser nulo');
        }

        try {

            $user = new UserCreateDto();
            $user->email = $loginDto->email_userName;
            $user->password = $loginDto->password;

            $result = $this->_userRepository->validateUser($user);

            if($result->isSuccess() == false)return $result;

            $userData = [
                'email' => $user->email,
                'ip' => $loginDto->lastLoginIp
            ];

            $this->verifyLastLoginAttempt($loginDto);

            $token =  $this->_twoFactorAuthService->generateTokenJwt($userData, $loginDto->remember);

            return new ResultOperation(
                true,
                'Login realizado com sucesso',
                [$token]
            );
        } catch (Exception $e) {

            return new ResultOperation(false, 'Erro login: ' . $e->getMessage());
        }
    }


    public function findUserJwt(string $token): ResultOperation
    {
        if($token == null) return new ResultOperation(false, 'Token nao pode ser null');

        try{

            $decodedToken  = $this->_twoFactorAuthService->verifyToken($token);

            if($decodedToken  == null ) return new ResultOperation(false, 'Token nao encontrado');

            $user = $this->_userRepository->findUserByEmailOrUsername($decodedToken->email);

            if($user == null) return new ResultOperation(false, 'Usuário nao encontrado');
            
            $userDto = $this->_mapperServiceResponse->mapUserToDto($user);
            return new ResultOperation(true, 'Usuário encontrado com sucesso', [$userDto]);

        }catch(Exception $e){
            return new ResultOperation(false, 'Erro ao buscar usuario: ' . $e->getMessage());
        }
    }

    /**
     * Busca um objeto Login por email_userName.
     * 
     * @param string $email_userName email_userName do Login a ser buscado.
     * @return Login O objeto Login encontrado ou null caso n encontre.
     */
    private function findLogin(string $email_userName): ?Login
    {
        return $this->getEntityManager()->getRepository(Login::class)
        ->findOneBy(['email_userName' => $email_userName]);
    }


    /**
     * Verifica se o  ultimo acesso do usuário foi mais de 1 minuto atrás.
     * Caso sim, atualiza o  ultimo acesso e retorna true.
     * Caso contrário, persiste o Login com o ultimo acesso atualizado e retorna false.
     * Se o Login náo existir, persiste um novo Login com os dados do $loginDto e retorna true.
     * 
     * @param LoginDto $loginDto Dados do Login a ser verificado.
     * @return bool True se o ultimo acesso foi mais de 1 minuto atrás, false caso contrário.
     */
    private  function verifyLastLoginAttempt(LoginDto $loginDto): bool
    {
        $login = $this->findLogin($loginDto->email_userName);

        if ($login == null) {
            $this->persistLogin($this->_mapperService->mapLogin($loginDto));
            return true;
        }

        $login->setLastLoginAttempt($login->getLastLoginAttempt());
        $login->setRemember($loginDto->remember);
        $this->persistLogin($login);
        
        return false;
    }


    /**
     * Valida um token JWT e verifica se o usuário está autenticado.
     * 
     * Verifica se o token é válido e se o usuário está autenticado.
     * Se sim, retorna um ResultOperation com a mensagem "Token autenticado com sucesso!" e o token.
     * Caso contrário, lança uma exceção com a mensagem de erro.
     * 
     * @param string $token O token JWT a ser validado.
     * @return ResultOperation A operação de resultado com a mensagem e o token.
     * @throws Exception Em caso de erro, lança uma exceção com a mensagem detalhada.
     */
    public function validateTokenJwt(string $token): ResultOperation
    {
        try {
            $token = $this->_twoFactorAuthService->verifyToken($token);
            $this->verifyLastSystemAccess($token->email);
            return new ResultOperation(true, 'Token autenticado com sucesso!', [$token]);
        } catch (Exception $e) {
            return new ResultOperation(false, 'Erro ao validar token:' . $e->getMessage());
        }
    }


    /**
     * Verifica a última acesso ao sistema para um usuário baseado em seu email.
     * 
     * Encontra o objeto Login do usuário pelo email, atualiza a data do último acesso
     * do sistema e persiste as alterações.
     * 
     * @param string $email_userName email do usuário a ser verificado.
     */
    private function verifyLastSystemAccess(string $email_userName): void {
        $login = $this->findLogin($email_userName);
        $login->setSystemAccess();
        $this->persistLogin($login);
    }

   
    /**
     * Initiates the account recovery process for a user based on their email.
     *
     * Validates the provided email and checks if the user exists in the repository.
     * If the email is invalid or the user is not found, returns an appropriate error message.
     * Checks if the user's account is activated and if the recovery token is expired.
     * If the account is not activated or the token is expired, returns a message to check the email.
     * Generates a new reset password token and sets its expiration time.
     * Persists the updated user data and sends a password recovery email to the user.
     * Returns a success message if the email is sent correctly, otherwise throws an exception.
     *
     * @param string $email_username The email of the user to initiate the account recovery.
     * @return ResultOperation The result of the operation with a success or error message.
     * @throws Exception In case of errors during the account recovery process.
     */

    public function recoveryAccount(string $email_username): ResultOperation
    {
        try{

            if(!filter_var($email_username, FILTER_VALIDATE_EMAIL))
                return new ResultOperation(false, 'Verifique o e-mail!');

            $result = $this->_userRepository->findUserByEmail($email_username);
            if($result->isSuccess() == false)return $result;
             
            $data = $result->getData();
            $user = $data[0];

            if($this->_userRepository->verifyTwoTokenFactorExpired($user, $email_username) == true)
               return new ResultOperation(false, 'Conta não ativada, verifique o email cadastrado!');


            if($this->_userRepository->verifyTokenExpiredRecoveryAccount($user) == true)
                return new ResultOperation(true, 'Verifique seu e-mail!');
             
            $token = $this->_twoFactorAuthService->generateToken();

            $user->setResetPasswordToken($token);
            $user->setResetPasswordTokenExpiresAt(new \DateTimeImmutable('+1 hour'));
            $this->persistLogin($user);
          
            $menssage = $this->_userRepository->sendPasswordRecoveryMessage(
                $user->getfullName(), $token);

            $this->_mailer->sendEmail(
                $user->isEmail($email_username),
                'Recuperar senha',
                $menssage,
            );

            return new ResultOperation(true, 'Verifique seu e-mail!');
            
        }catch(Exception $e) {

            return new ResultOperation(true, 'Erro:'. $e->getMessage());
        }
    }

    public function logaut(?string $token): ResultOperation
    {
        if(empty($token)) return new ResultOperation(false, 'Token nao pode ser nulo');

        try{
            $result = $this->_twoFactorAuthService->invalidatingToken($token);
            return new ResultOperation(true, 'Deslogado com sucesso', [$result]);

        }catch (Exception $e){
            return new ResultOperation(false, 'Erro ao deslogar: ' . $e->getMessage());
        }
    }

    /**
     * Persiste um objeto Login no banco de dados.
     * 
     * @param Login $login O objeto Login a ser persistido.
     */
    private function persistLogin(Object $object): void
    {
        $this->getEntityManager()->persist($object);
        $this->getEntityManager()->flush();
    }
}

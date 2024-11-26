<?php

namespace App\Repository;

use App\Entity\Login;
use App\Entity\User;
use App\Dto\Create\UserCreateDto;
use App\Dto\LoginDto;
use DateTime;
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
use phpDocumentor\Reflection\Types\Boolean;
use PhpParser\Node\Expr\Cast\Object_;

/**
 * @extends ServiceEntityRepository<Login>
 */
class LoginRepository extends ServiceEntityRepository implements LoginRepositoryInterface
{
    private MapperService $_mapperService;
    private EmailService $_mailer;
    private TwoFactorAuthService $_twoFactorAuthService;
    private UserRepositoryInterface $_userRepository;

    public function __construct(
        ManagerRegistry $registry,
        MapperService $mapperService,
        EmailService $mailer,
        TwoFactorAuthService $twoFactorAuthService,
        UserRepositoryInterface $userRepository
    ) {
        parent::__construct($registry, Login::class);
        $this->_mapperService = $mapperService;
        $this->_mailer = $mailer;
        $this->_twoFactorAuthService = $twoFactorAuthService;
        $this->_userRepository = $userRepository;
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

            if ($this->verifyLastLoginAttempt($loginDto)) { 

                $token =  $this->_twoFactorAuthService->generateTokenJwt($userData);
                return new ResultOperation(true, 'Login realizado com sucesso', [$token]);
            }

            $token =  $this->_twoFactorAuthService->generateTokenJwt($userData);

            return new ResultOperation(
                true,
                'Login realizado com sucesso',
                [$token]
            );
        } catch (Exception $e) {

            return new ResultOperation(false, 'Erro login: ' . $e->getMessage());
        }
    }

    /**
     * Busca um objeto Login por email_userName.
     * 
     * @param string $email_userName email_userName do Login a ser buscado.
     * @return Login O objeto Login encontrado ou null caso n encontre.
     */
    private function findLogin(string $email_userName): Login
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

        if (!$login) {
            $this->persistLogin(new Login(
                $loginDto->email_userName,
                $loginDto->password,
                $loginDto->lastLoginIp
            ));
            return true;
        }

        $login->setLastLoginAttempt($login->getLastLoginAttempt());
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
    public function validadteTokenJwt(string $token): ResultOperation
    {
        try {
            $token = $this->_twoFactorAuthService->verifyToken($token);
            $this->verifyLastSystemAccess($token->email);
            return new ResultOperation(true, 'Token autenticado com sucesso!', [$token]);
        } catch (Exception $e) {
            return new ResultOperation(false, 'Erro ao validar token:' . $e->getMessage());
        }
    }

    private function verifyLastSystemAccess(string $email_userName): void {
        $login = $this->findLogin($email_userName);

        $login->setSystemAccess();
        $this->persistLogin($login);
    }

   
    /**
     * Inicia o processo de recuperação de conta para um usuário.
     *
     * Verifica se o email fornecido é válido. Se o email for inválido, retorna uma mensagem de erro.
     * Caso contrário, busca o usuário no repositório. Se o usuário não for encontrado, retorna uma falha.
     * Se o usuário for encontrado, gera um token de autenticação de dois fatores e o atribui ao usuário.
     * Persiste as alterações no login do usuário e envia um email com o token para recuperação de senha.
     * Retorna sucesso se o email foi enviado corretamente. Em caso de erro, uma exceção é lançada.
     *
     * @param string $email_username O email do usuário para iniciar a recuperação de conta.
     * @return ResultOperation A operação de resultado com mensagem de sucesso ou erro.
     * @throws Exception Em caso de erro durante o processo de recuperação de conta.
     */

    public function recoveryAccount(string $email_username): ResultOperation
    {
        try{

            if(!filter_var($email_username, FILTER_VALIDATE_EMAIL))
                return new ResultOperation(false, 'Verifique o e-mail cadastrado!');

            $result = $this->_userRepository->findUserByEmail($email_username);
            if($result->isSuccess() == false)return $result;
             
            $data = $result->getData();
            $user = $data[0];

            $token = $this->_twoFactorAuthService->generateToken();

            $user->setTwoFactorToken($token);
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

        /**
     * Persiste um objeto Login no banco de dados.
     * 
     * @param Login $login O objeto Login a ser persistido.
     */
    private function persistLogin(Object $login): void
    {
        $this->getEntityManager()->persist($login);
        $this->getEntityManager()->flush();
    }
}

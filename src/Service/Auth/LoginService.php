<?php

namespace App\Service\Auth;

use App\Dto\Create\UserCreateDto;
use App\Dto\LoginDto;
use App\Entity\Enum\TypeEntitiesEnum;
use App\Entity\User;
use App\Interface\Repository\BaseRepositoryInterface;
use App\Interface\Service\LoginServiceInterface;
use App\Interface\Service\UserServiceInterface;
use App\Repository\BaseRepository;
use App\Service\Auth\TwoFactorAuthService;
use App\Service\Email\EmailService;
use App\Service\Mapper\MapperService;
use App\Service\Util\ResultOperationService;
use App\Util\DoctrineFindParams;
use App\Util\PayLoadJwt;
use App\Util\ResultOperation;
use DateTimeInterface;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

class LoginService extends BaseRepository implements LoginServiceInterface
{
    private MapperService $_mapperService;
    private EmailService $_mailer;
    private TwoFactorAuthService $_twoFactorAuthService;
    private UserServiceInterface $_userServiceInterface;
    private ResultOperationService $_resultOperationService;

    public function __construct(
        ManagerRegistry $registry,
        MapperService $mapperService,
        EmailService $mailer,
        TwoFactorAuthService $twoFactorAuthService,
        UserServiceInterface $userServiceInterface,
        ResultOperationService $resultOperationService,
    ) {
        parent::__construct($registry, TypeEntitiesEnum::LOGIN);
        $this->_mapperService = $mapperService;
        $this->_mailer = $mailer;
        $this->_twoFactorAuthService = $twoFactorAuthService;
        $this->_userServiceInterface = $userServiceInterface;
        $this->_resultOperationService = $resultOperationService;
    }

    public function Login(LoginDto $loginDto): ResultOperation
    {
        if (empty($loginDto)) return $this->_resultOperationService->createFailure(
            'Login não pode ser nulo'
        );

        $result = $this->validateLoginUser($loginDto);

        if (!$result->isSuccess()) return $result;

        $this->verifyLastLoginAttempt($loginDto);

        $token = $this->_twoFactorAuthService->generateTokenJwt(
            $this->peprorationPayLoadJwtSession($loginDto),
            $loginDto->remember
        );

        return $this->_resultOperationService->createSuccess(
            'Usuário logado com sucesso.',
            [$token]
        );
    }

    private function validateLoginUser(LoginDto $loginDto): ResultOperation
    {
        $userDto = new UserCreateDto();
        $userDto->email = $loginDto->emailUserName;
        $userDto->password = $loginDto->password;

        return $this->_userServiceInterface->validateUser($userDto);
    }

    private function peprorationPayLoadJwtSession(LoginDto $loginDto): PayLoadJwt
    {
        $payLoadJwtSession = $this->createPayLoadJwt();
        $payLoadJwtSession->addProperty( 'email', $loginDto->emailUserName);
        $payLoadJwtSession->addProperty('ip', $loginDto->lastLoginIp);
        return $payLoadJwtSession;
    }

    private function verifyLastLoginAttempt(LoginDto $loginDto): bool
    {
        $params = $this->createDoctrineFindParams('email', $loginDto->emailUserName, TypeEntitiesEnum::LOGIN);
        $login = $this->getEntity($params);

        if ($login == null) {
            $this->updateEntity($this->_mapperService->mapLogin($loginDto), $params);
            return true;
        }

        $login->setLastLoginAttempt($login->getLastLoginAttempt());
        $login->setRemember($loginDto->remember);
        $this->updateEntity($login, $params);

        return false;
    }

    public function validateTokenJwt(string $token): ResultOperation
    {
        $token = $this->_twoFactorAuthService->verifyToken($token);
        $this->verifyLastSystemAccess($token->email);
        return $this->_resultOperationService->createSuccess('Token autenticado com sucesso!', [$token]);
    }

    private function verifyLastSystemAccess(string $email_userName): void
    { 
        $params = $this->createDoctrineFindParams('email_userName', $email_userName, TypeEntitiesEnum::LOGIN);
        $login = $this->getEntityOneBy($params);
        $login->setLastLoginAttempt();
        $this->persist($login);
    }

    public function recoveryAccount(string $email_username): ResultOperation
    {
        return $this->handleRecoveryAccount($email_username);
    }

    private function handleRecoveryAccount(string $email_username): ResultOperation
    {
        if (filter_var($email_username, FILTER_VALIDATE_EMAIL) !== false) {
            return $this->_resultOperationService->createFailure('Verifique o e-mail!');
        }

        $user = $this->_userServiceInterface->findUserByEmailOrUsername($email_username);

        if ($user == null) {
            return $this->_resultOperationService->createFailure('Usuário não encontrado');
        }

        return $this->processRecoveryAccount($user, $email_username);
    }

    private function processRecoveryAccount($user, string $email_username): ResultOperation
    {
        switch (true) {
            case $this->_userServiceInterface->verifyTwoTokenFactorExpired($user, $email_username);
                return $this->_resultOperationService->createFailure(
                    'Conta não ativada, verifique o email cadastrado!');
            case $this->_userServiceInterface->verifyTokenExpiredRecoveryAccount($user);
                return $this->_resultOperationService->createSuccess('Verifique seu e-mail!');
            default:
                $this->generateAndSendRecoveryToken($user);
                return $this->_resultOperationService->createSuccess('Verifique seu e-mail!');
        }
    }

    private function generateAndSendRecoveryToken($user): void
    {
        $token = $this->_twoFactorAuthService->generateToken();
        $user->setResetPasswordToken($token);
        $user->setResetPasswordTokenExpiresAt(new \DateTimeImmutable('+1 hour'));
        $this->persist($user);

        $message = $this->_userServiceInterface->sendPasswordRecoveryMessage($user->getFullName(), $token);
        $this->_mailer->sendEmail($user->getEmail(), 'Recuperar senha', $message);
    }

    private function createDoctrineFindParams(string $field, 
    string $value, TypeEntitiesEnum $entityType): DoctrineFindParams
    {
        return new DoctrineFindParams($field, $value, $entityType);
    }

    private function createPayLoadJwt(): PayLoadJwt
{
    $payLoadJwt = new PayLoadJwt();
    return $payLoadJwt;
}
}
<?php

use App\Dto\Create\UserCreateDto;
use App\Repository\UserRepository;
use App\Service\MapperServiceCreate;
use App\Service\MapperServiceResponse;
use App\Service\EmailService;
use App\Service\TwoFactorAuthService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Repository\RoleRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Tests\Dependency\DatabaseTestCase;
use Tests\Dependency\User\UserDependencies;

class UserRepositoryTest extends KernelTestCase
{
    private UserRepository $_userRepository;
    private $_twoFactorAuthService;

    protected function setUp(): void
    {
        $factory = new UserDependencies();
        $this->_userRepository =  $factory->userRepository();
        $this->_twoFactorAuthService = static::getContainer()->get(TwoFactorAuthService::class)->generateToken();
    }
    public function testUserRepository(): void
    {
        $result = $this->_userRepository->createUser(UserDataTest::createUser());
        $this->assertTrue($result->isSuccess());
        $this->assertSame('Usuário criado com sucesso. Verifique sua caixa de email para ativação da conta.',
             $result->getMessage());

        $this->testEnableTwoFactorAuth();
        $this->testSendWelcomeMessage();
        $this->testSendPasswordRecoveryMessage();
        $this->testValidateUserIsNotVerify();
        // $this->testVerifyTwoTokenFactorExpired();
        $this->testFindUserByEmail(UserDataTest::createUser()->email);
    }

    private function testEnableTwoFactorAuth(): void 
    {
        $result = $this->_userRepository->enableTwoFactorAuth(
           $this->_twoFactorAuthService);

        $this->assertFalse($result->isSuccess());
    }

    private function testSendWelcomeMessage(): void 
    {
        $result = $this->_userRepository->sendWelcomeMessage('Teste',
                         $this->_twoFactorAuthService);

        $this->assertNotNull($result);
    }

    private function testSendPasswordRecoveryMessage(): void
    {
        $result = $this->_userRepository->sendPasswordRecoveryMessage(
            'Teste', $this->_twoFactorAuthService);

        $this->assertNotNull($result);
    }

    private function testValidateUserIsNotVerify(): void
    {
        $result = $this->_userRepository->validateUser(UserDataTest::createUser());

        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame(
            "Conta não ativada, verifique o email cadastrado!", $result->getMessage());
    }

    private function testValidadeUser(): void 
    {

    }

    private function testVerifyTwoTokenFactorExpired(): void 
    {
        $result = $this->_userRepository->validateUser(
            UserDataTest::createUser());

        $this->assertNotNull($result);
        $this->assertTrue($result);
        $this->assertSame("Usuário valídado com sucesso!",
         $result->getMessage());
    }

    private function testFindUserByEmail(string $email): void
    {
        $result  = $this->_userRepository->findUserByEmail(UserDataTest::createUser()->email);

        $this->assertTrue($result->isSuccess());
    }
}

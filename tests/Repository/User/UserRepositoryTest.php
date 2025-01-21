<?php

use App\Dto\Create\UserCreateDto;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Dependency\User\UserDependencies;
use Tests\DataFixtures\Entity\UserDataTest;

class UserRepositoryTest extends KernelTestCase
{
    private UserRepository $_userRepository;

    protected function setUp(): void
    {
        $factory = new UserDependencies();
        $this->_userRepository =  $factory->userRepository();
    }
    public function testUserRepository(): void
    {
        $this->assertInstanceOf(UserCreateDto::class, UserDataTest::createUser());

        $result = $this->_userRepository->createUser(new UserCreateDto());
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Usuário não pode ser nulo',
            $result->getMessage());

        $result = $this->_userRepository->createUser(UserDataTest::createUser());
        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('Usuário criado com sucesso. Verifique sua caixa de email para ativação da conta.',
             $result->getMessage());

        $result = $this->_userRepository->createUser(UserDataTest::createUser1());
        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('Usuário criado com sucesso. Verifique sua caixa de email para ativação da conta.',
            $result->getMessage());

        $result = $this->_userRepository->createUser(UserDataTest::createUser());
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Usuário já existe',
            $result->getMessage());


        $this->testEnableTwoFactorAuth();

        $this->testFindUserByEmail();
    }

    private function testEnableTwoFactorAuth(): void 
    {
        $user = UserDependencies::findUserByEmail(UserDataTest::createUser()->email);
        $result = $this->_userRepository->enableTwoFactorAuth($user->getTwoFactorToken());

        $this->assertTrue($result->isSuccess());

        $this->testSendWelcomeMessage();
    }

    private function testSendWelcomeMessage(): void 
    {
        $user = UserDependencies::findUserByEmail(UserDataTest::createUser()->email);
        $result = $this->_userRepository->sendWelcomeMessage('Teste',
                         $user->getTwoFactorToken());

        $this->assertNotNull($result);

        $this->testSendPasswordRecoveryMessage();
    }

    private function testSendPasswordRecoveryMessage(): void
    {
        // $user = UserDependencies::findUserByEmail(UserDataTest::createUser()->email);
        // $result = $this->_userRepository->sendPasswordRecoveryMessage(
        //     'Teste', $user->getResetPasswordToken());

        // $this->assertNotNull($result);

        $this->testValidadeUser();
    }

    private function testValidadeUser(): void
    {
        $result = $this->_userRepository->validateUser(UserDataTest::createUser());

        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame(
            "Usuário valídado com sucesso!", $result->getMessage());

        $this->testUserExist();
    }

    private function testUserExist(): void
    {
        $result = $this->_userRepository->userExists('teste');

        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());

        $result = $this->_userRepository->userExists(UserDataTest::createUser()->email);

        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());

        $this->testVerifyTwoTokenFactorExpired();
    }

    private function testVerifyTwoTokenFactorExpired(): void 
    {
        $user = UserDependencies::findUserByEmail(UserDataTest::createUser()->email);
        $result = $this->_userRepository->verifyTwoTokenFactorExpired(
            $user, UserDataTest::createUser()->email);

        $this->assertNotNull($result);
        $this->assertFalse($result);

        $this->testVerifyTokenRecoveryAccount();
    }

    private function testVerifyTokenRecoveryAccount(): void
    {
        $user = UserDependencies::findUserByEmail(UserDataTest::createUser()->email);

        $result = $this->_userRepository->verifyTokenRecoveryAccount('');

        $this->assertNotNull($result);
        $this->assertFalse($result);

        $this->testVerifyTokenExpiredRecoveryAccount();
    }

    private function testVerifyTokenExpiredRecoveryAccount(): void
    {
        $user = UserDependencies::findUserByEmail(UserDataTest::createUser()->email);

        $result = $this->_userRepository->verifyTokenExpiredRecoveryAccount($user);

        $this->assertNotNull($result);
        $this->assertFalse($result);

        $this->testUpdateUser();
    }

    private function testUpdateUser(): void 
    {
        $user = UserDependencies::findUserByEmail(UserDataTest::createUser()->email);
        $userUpdate = UserDataTest::updateUser();

        $result = $this->_userRepository->updateUser('', $userUpdate);
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Identificador não pode ser null.', $result->getMessage());

        $result = $this->_userRepository->updateUser($user->getId(), new UserCreateDto());
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Usuário não pode ser null.', $result->getMessage());

        $result = $this->_userRepository->updateUser($user->getId(),$userUpdate);
        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('Usuário atualizado com sucesso', $result->getMessage());

        $this->testDeleteUserById();
    }

    private function testDeleteUserById(): void
    {
        $user = $this->_userRepository->findUserByEmail(UserDataTest::createUser1()->email);
        $data = $user->getData()[0];

        $result = $this->_userRepository->deleteUserById('');
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Identificador não pode ser null.', $result->getMessage());

        $result = $this->_userRepository->deleteUserById($data->id);
        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('Usuário deletado com sucesso', $result->getMessage());

        $result = $this->_userRepository->deleteUserById($data->id);
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Usuário não encontrado', $result->getMessage());

        $this->testFindUserById();
    }

    private function testFindUserById(): void 
    {
        $user = $this->_userRepository->findUserByEmail(UserDataTest::updateUser()->email);
        $data = $user->getData()[0];

        $result = $this->_userRepository->findUserById('');
        
        $this->assertNotNull($result);
        $this->assertFalse( $result->isSuccess());
        $this->assertSame('Identificador não pode ser null.', $result->getMessage());

        $result = $this->_userRepository->findUserById('idNãoExiste');
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Usuário não encontrado.', $result->getMessage());

        $result = $this->_userRepository->findUserById($data->id);
        $this->assertNotNull($result);
        $this->assertTrue( $result->isSuccess());
        $this->assertSame('Usuário encontrado com sucesso.', $result->getMessage());

        $this->testFindUserByEmail();
    }

    private function testFindUserByEmail(): void
    {
        $result  = $this->_userRepository->findUserByEmail('');
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Email inválido', $result->getMessage());

        $result  = $this->_userRepository->findUserByEmail('emailNãoCadastrado');
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Email inválido', $result->getMessage());

        $result = $this->_userRepository->findUserByEmail('email@teste.com.br');
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Usuário não existe', $result->getMessage());

        $result = $this->_userRepository->findUserByEmail(UserDataTest::updateUser()->email);
        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('Usuário encontrado com sucesso!', $result->getMEssage());

        $this->findUserByUserName();
    }

    private function findUserByUserName(): void 
    {
        $user = $this->_userRepository->findUserByEmail(UserDataTest::updateUser()->email);
        $data = $user->getData()[0];

        $result = $this->_userRepository->findUserByUserName('');
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Nome do usuário null.', $result->getMessage());

        $result = $this->_userRepository->findUserByUserName('nomeNaoCadastrado');
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Usuário não existe.', $result->getMessage());

        $result = $this->_userRepository->findUserByUserName($data->userName);
        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('Usuário encontrado com sucesso.', $result->getMessage());

        $this->testFindByDocument();
    }

    private function testFindByDocument(): void
    {
        $user = $this->_userRepository->findUserByEmail(UserDataTest::updateUser()->email);
        $data = $user->getData()[0];

        $result = $this->_userRepository->findUserByDocument('');
        $this->assertNotNull($result);        
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Cpf ou cnpj do usuário null.', $result->getMessage());

        $result = $this->_userRepository->findUserByDocument('documentoNaoCadastrado');
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Usuário nao encontrado.', $result->getMessage());

        $result = $this->_userRepository->findUserByDocument($data->cnpjCpfRg);
        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('Usuário encontrado com sucesso.', $result->getMessage());

        $this->testConfirmPasswordReset();
    }

    private function testConfirmPasswordReset(): void
    {
        $user = $this->_userRepository->findUserByEmail(UserDataTest::updateUser()->email);
        $data = $user->getData()[0];

        $result = $this->_userRepository->confirmPasswordReset('', ''); 
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Token e senha não podem ser vazias.', $result->getMessage());

        // $result = $this->_userRepository->confirmPasswordReset($data->passwordResetToken, $data->password);  
        // $this->assertNotNull($result);
        // $this->assertFalse($result->isSuccess());
        // $this->assertSame('Token expírado.', $result->getMessage());

        // $result = $this->_userRepository->confirmPasswordReset($data->passwordResetToken, $data->password);
        // $this->assertNotNull($result);
        // $this->assertTrue($result->isSuccess());
        // $this->assertSame('Senha redefinida com sucesso.', $result->getMessage());
    }
}

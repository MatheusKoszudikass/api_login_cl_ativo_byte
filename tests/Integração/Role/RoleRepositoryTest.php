<?php

use App\Dto\Create\RoleCreateDto;
use App\Repository\RoleRepository;
use App\Util\ResultOperation;
use Tests\Dependency\Role\RoleDependencies;
use Tests\DataFixtures\Entity\RoleDataTest;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class RoleRepositoryTest extends KernelTestCase
{
    private RoleRepository $_roleRepository;
    private RoleDependencies $_roleDependencies;

    protected function setUp(): void
    {
        $this->_roleDependencies = new RoleDependencies();
        $this->_roleRepository =  $this->_roleDependencies->roleRepository();
    }

    public function testRoleRepository(): void 
    {
        $this->assertInstanceOf(RoleCreateDto::class, RoleDataTest::createRole());

        $result = $this->_roleRepository->createRole(RoleDataTest::createRole());
        $this->assertInstanceOf(ResultOperation::class, $result);
        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('Role criado com sucesso.', $result->getMessage());

        $result = $this->_roleRepository->createRole(RoleDataTest::createRole1());
        $this->assertInstanceOf(ResultOperation::class, $result);
        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('Role criado com sucesso.', $result->getMessage());

        $result = $this->_roleRepository->createRole(RoleDataTest::createRole());
        $this->assertInstanceOf(ResultOperation::class, $result);
        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('Role encontrado com sucesso.', $result->getMessage());

        $reuslt = $this->_roleRepository->createRole(new RoleCreateDto());
        $this->assertInstanceOf(ResultOperation::class, $reuslt);
        $this->assertNotNull($reuslt);
        $this->assertFalse($reuslt->isSuccess());
        $this->assertSame('Role não pode ser null.', $reuslt->getMessage());

        $this->testRoleExists();
    }

    private function testRoleExists(): void 
    {
        $result = $this->_roleRepository->roleExists('Administrador');
        $this->assertInstanceOf(ResultOperation::class, $result);
        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('Role encontrado com sucesso.', $result->getMessage());

        $result = $this->_roleRepository->roleExists('adm');
        $this->assertInstanceOf(ResultOperation::class, $result);
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Role não encontrado.', $result->getMessage());


        $result = $this->_roleRepository->roleExists('');
        $this->assertInstanceOf(ResultOperation::class, $result);
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Nome null.', $result->getMessage());

        $this->testUpdateRole();
    }

    private function testUpdateRole(): void 
    {
        $this->assertInstanceOf(RoleCreateDto::class, RoleDataTest::updateRole());

        $role = $this->_roleDependencies->findRoleByName(RoleDataTest::createRole()->name);

        $result = $this->_roleRepository->updateRole('',new RoleCreateDto());
        $this->assertInstanceOf(ResultOperation::class, $result);
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Identificador não pode ser null.', $result->getMessage());

        $result = $this->_roleRepository->updateRole($role->getId(),new RoleCreateDto());
        $this->assertInstanceOf(ResultOperation::class, $result);
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Role não pode ser null.', $result->getMessage());

        $result = $this->_roleRepository->updateRole('$role->getId()',RoleDataTest::createRoleFixtures());
        $this->assertInstanceOf(ResultOperation::class, $result);
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Nenhuma role encontrada com o identificador fornecido.', $result->getMessage());

        $result = $this->_roleRepository->updateRole($role->getId(),RoleDataTest::updateRole());
        $this->assertInstanceOf(ResultOperation::class, $result);
        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('Role atualizado com sucesso.', $result->getMessage());

        $this->testFindRoleById();
    }

    private function testFindRoleById(): void
    {
        $role = $this->_roleDependencies->findRoleByName(RoleDataTest::createRole1()->name);

        $result = $this->_roleRepository->findRoleById('');
        $this->assertInstanceOf(ResultOperation::class, $result);
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Identificador não pode ser null.', $result->getMessage());

        $result = $this->_roleRepository->findRoleById('dsadsadsadsadas');
        $this->assertInstanceOf(ResultOperation::class, $result);
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Nenhuma role encontrada com o identificador fornecido.', $result->getMessage());

        $result = $this->_roleRepository->findRoleById($role->getId());
        $this->assertInstanceOf(ResultOperation::class, $result);
        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('Role encontrado com sucesso.', $result->getMessage());

        $this->testFindRoleByName();
    }

    private function testFindRoleByName(): void 
    {
        $result = $this->_roleRepository->findRoleByName('');
        $this->assertInstanceOf(ResultOperation::class, $result);
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Identificador não pode ser null.', $result->getMessage());

        $result = $this->_roleRepository->findRoleByName(RoleDataTest::createRole()->name);
        $this->assertInstanceOf(ResultOperation::class, $result);
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Nenhuma role encontrada com o identificador fornecido.', $result->getMessage()); 

        $result = $this->_roleRepository->findRoleByName(RoleDataTest::createRole1()->name);
        $this->assertInstanceOf(ResultOperation::class, $result);
        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('Role encontrado com sucesso.', $result->getMessage());

        $this->testFindRoleAll();

    }

    private function testFindRoleAll(): void
    {
        $result = $this->_roleRepository->findRoleAll();
        $this->assertInstanceOf(ResultOperation::class, $result);
        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('Roles sucesso.', $result->getMessage());

        $this->testDeleteRole();
    }

    private function testDeleteRole(): void 
    {
        $role = $this->_roleDependencies->findRoleByName(RoleDataTest::createRole1()->name);

        $result = $this->_roleRepository->deleteRole('');
        $this->assertInstanceOf(ResultOperation::class, $result);
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Identificador não pode ser null.', $result->getMessage());


        $result = $this->_roleRepository->deleteRole('$role->getId()');
        $this->assertInstanceOf(ResultOperation::class, $result);
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Nenhuma role encontrada com o identificador fornecido.', $result->getMessage());

        $result = $this->_roleRepository->deleteRole($role->getId());
        $this->assertInstanceOf(ResultOperation::class, $result);
        $this->assertNotNull($result);
        $this->assertTrue($result->isSuccess());
        $this->assertSame('Role deletado com sucesso.', $result->getMessage());

        $result = $this->_roleRepository->findRoleById($role->getId());
        $this->assertInstanceOf(ResultOperation::class, $result);
        $this->assertNotNull($result);
        $this->assertFalse($result->isSuccess());
        $this->assertSame('Nenhuma role encontrada com o identificador fornecido.', $result->getMessage());
    }
}
<?php

namespace App\Repository;

use App\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Interface\Repository\RoleRepositoryInterface;
use App\Dto\Create\RoleCreateDto;
use App\Util\ResultOperation;
use App\Service\Mapper\MapperCreateService;
use App\Service\Mapper\MapperResponseService;
use Exception;

class RoleRepository extends ServiceEntityRepository implements RoleRepositoryInterface
{
    private MapperCreateService $_mapperCreateService;
    private MapperResponseService $_mapperResponseService;

    public function __construct(ManagerRegistry $registry, MapperCreateService $mapperCreateService,
     MapperResponseService $mapperResponseService)
    {
        parent::__construct($registry, Role::class);
        $this->_mapperCreateService = $mapperCreateService;
        $this->_mapperResponseService = $mapperResponseService;
    }


    public function createRole(RoleCreateDto $role): ResultOperation
    {
        if (!$role->isEmpty()) return new ResultOperation(false, 'Role não pode ser null.');

        try {
            $result = $this->roleExists($role->name);
            if ($result->isSuccess() == true) return $result;
            
            $role = $this->_mapperCreateService->mapRole($role);
            $this->getEntityManager()->persist($role);
            $this->getEntityManager()->flush();

            $result = $this->_mapperResponseService->mapRoleToDto($role);

            return new ResultOperation(true, 'Role criado com sucesso.');

        } catch (Exception $exception) {
            return new ResultOperation(false, $exception->getMessage());
        }
    }

    public function roleExists(string $name): ResultOperation
    {
        if($name == null || $name === '') return new ResultOperation(
            false, 'Nome null.');

        try {
            $queryBuilder = $this->getEntityManager()->createQueryBuilder();
            $queryBuilder->select('r')
                ->from(Role::class, 'r')
                ->where('r.name = :name')
                ->setParameter('name', $name);
                
            if($queryBuilder->getQuery()->getOneOrNullResult() !== null) return new ResultOperation(
                true, 'Role encontrado com sucesso.');
            
            return new ResultOperation(false, 'Role não encontrado.');

        } catch (Exception $exception) {
            return new ResultOperation(false, 'Erro ao buscar role pelo nome.'. $exception->getMessage());
        }     

    }

    public function updateRole(string $id, RoleCreateDto $roleDto): ResultOperation
    {
        if(empty($id)|| $id == null) return new ResultOperation(
            false, 'Identificador não pode ser null.');

        if(!$roleDto->isEmpty()) return new ResultOperation(
            false, 'Role não pode ser null.');
        

        try {
            $roleExists = $this->getEntityManager()->getRepository(Role::class)->findOneBy(['id' => $id]);

            if($roleExists == null) return new ResultOperation(
                false, "Nenhuma role encontrada com o identificador fornecido.");

            $roleExists->setName($roleDto->name);
            $roleExists->setDescription($roleDto->description);

            $this->getEntityManager()->persist($roleExists);
            $this->getEntityManager()->flush();

            return new ResultOperation(true, 'Role atualizado com sucesso.');

        } catch (Exception $exception) {
            return new ResultOperation(false, $exception->getMessage());
        }
    }


    public function findRoleById(string $id): ?ResultOperation
    {
        if (empty($id)) return new ResultOperation(
            false, 'Identificador não pode ser null.');

        try {
            $role = $this->getEntityManager()->getRepository(Role::class)->findOneBy(
                ['id'=> $id]);
           
            if ($role == null) return new ResultOperation(
                false, 'Nenhuma role encontrada com o identificador fornecido.');

            $result = $this->_mapperResponseService->mapRoleToDto($role);

            return new ResultOperation(true, 'Role encontrado com sucesso.', data: [$result]);

        } catch (Exception $e) {
            
            return new ResultOperation(false, "Erro: " . $e->getMessage());
        }
    }

    public function findRoleByName(string $name): ?ResultOperation
    {
        if(empty($name)) return new ResultOperation(
            false, 'Identificador não pode ser null.');

        try{

            $role = $this->getEntityManager()->getRepository(
                Role::class)->findOneBy(['name' => $name]);

            if($role == null)
            {
                return new ResultOperation(false, 'Nenhuma role encontrada com o identificador fornecido.');
            }

            $result = $this->_mapperResponseService->mapRoleToDto($role);

            return new ResultOperation(true, 'Role encontrado com sucesso.', data: [$result]);

        }catch(Exception $e){

            return new ResultOperation(false, "Erro: " . $e->getMessage());
        }
    }

    public function findRoleAll(): ?ResultOperation
    {
        try {
            $roles = $this->getEntityManager()->getRepository(Role::class)->findAll();
            $roleResponseDtos = [];
            foreach ($roles as $role) {
                $roleResponseDto = $this->_mapperResponseService->mapRoleToDto($role);
                $roleResponseDtos[] = $roleResponseDto;
            }
            return new ResultOperation(true, 'Roles sucesso.',  $roleResponseDtos);
        } catch (Exception $exception) {
            return new ResultOperation(false, 'Erro ao buscar roles:'. $exception->getMessage());
        }
    }

    public function deleteRole(string $id): ResultOperation
    {
        if (empty($id)) return new ResultOperation(
                false, 'Identificador não pode ser null.');

        try {
            $role = $this->getEntityManager()->getRepository(Role::class)->find($id);
            if($role == null)return new ResultOperation(
                false, 'Nenhuma role encontrada com o identificador fornecido.');

            $this->getEntityManager()->remove($role);
            $this->getEntityManager()->flush();
            return new ResultOperation(true, 'Role deletado com sucesso.');
        } catch (Exception $exception) {
            return new ResultOperation(false, $exception->getMessage());
        }
    }
}

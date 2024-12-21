<?php

namespace App\Repository;

use App\Entity\Role;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Interface\RoleRepositoryInterface;
use App\Dto\Create\RoleCreateDto;
use App\Dto\Response\RoleResponseDto;
use App\Result\ResultOperation;
use App\Service\MapperServiceCreate;
use App\Service\MapperServiceResponse;
use Exception;
use LDAP\Result;

/**
 * @extends ServiceEntityRepository<Role>
 */
class RoleRepository extends ServiceEntityRepository implements RoleRepositoryInterface
{
    private MapperServiceCreate $_mapperServiceCreate;
    private MapperServiceResponse $_mapperServiceResponse;

    public function __construct(ManagerRegistry $registry, MapperServiceCreate $mapperServiceCreate, MapperServiceResponse $mapperServiceResponse)
    {
        parent::__construct($registry, Role::class);
        $this->_mapperServiceCreate = $mapperServiceCreate;
        $this->_mapperServiceResponse = $mapperServiceResponse;
    }


    public function createRole(RoleCreateDto $role): ResultOperation
    {
        if ($role === null) {
            return new ResultOperation(false, 'Role não pode ser nulo');
        }

        try {
            $role = $this->_mapperServiceCreate->mapRole($role);
            $this->getEntityManager()->persist($role);
            $this->getEntityManager()->flush();

            $result = $this->_mapperServiceResponse->mapRoleToDto($role);

            return new ResultOperation(true, 'Role criado com sucesso', [$result]);

        } catch (Exception $exception) {
            return new ResultOperation(false, $exception->getMessage());
        }
    }

    public function updateRole(RoleCreateDto $role): ResultOperation
    {
        if ($role === null) {
            return new ResultOperation(false, 'Role não pode ser nulo');
        }

        try {
            $role = $this->_mapperServiceCreate->mapRole($role);
            $this->getEntityManager()->persist($role);
            $this->getEntityManager()->flush();

            return new ResultOperation(true, 'Role atualizado com sucesso');

        } catch (Exception $exception) {
            return new ResultOperation(false, $exception->getMessage());
        }
    }


    public function findRoleById(string $id): ?ResultOperation
    {
        if ($id == null) {
            return new ResultOperation(false, 'O identificador da role não pode estar vazio.');
        }

        try {
            
            $role = $this->getEntityManager()->getRepository(Role::class)->findOneBy(
                ['id'=> $id]);
           
            if ($role == null) {
                return new ResultOperation(false, 'Nenhuma role encontrada com o identificador fornecido.');
            }

            $result = $this->_mapperServiceResponse->mapRoleToDto($role);

            return new ResultOperation(true, 'Role encontrada com sucesso!', data: [$result]);

        } catch (Exception $e) {
            
            return new ResultOperation(false, "Erro: " . $e->getMessage());
        }
    }

    public function findRoleByName(string $name): ?ResultOperation
    {
        if($name == null)
        {
            return new ResultOperation(false, 'O nome da role não pode estar vazio.');
        }

        try{

            $role = $this->getEntityManager()->getRepository(
                Role::class)->findOneBy(['name' => $name]);

            if($role == null)
            {
                return new ResultOperation(false, 'Nenhuma role encontrada com o nome fornecido.');
            }

            $result = $this->_mapperServiceResponse->mapRoleToDto($role);

            return new ResultOperation(true, 'Role encontrada com sucesso!', data: [$result]);

        }catch(Exception $e){

            return new ResultOperation(false, "Erro: " . $e->getMessage());
        }
    }

    public function findRoleAll(): ?ResultOperation
    {
        try {
            $roles = $this->getEntityManager()->getRepository(Role::class)->findAll();
            // $roleResponseDtos = [];
            // foreach ($roles as $role) {
            //     $roleResponseDto = $this->_mapperServiceResponse->mapRoleToDto($role);
            //     $roleResponseDtos[] = $roleResponseDto;
            // }
            return new ResultOperation(true, 'Roles encontradas com sucesso!',  $roles);
        } catch (Exception $exception) {
            return new ResultOperation(false, 'Erro ao buscar roles:'. $exception->getMessage());
        }
    }

    public function deleteRole(string $id): ResultOperation
    {
        if ($id == null) {
            return new ResultOperation(false, 'Id não pode ser nulo');
        }

        try {

            $role = $this->getEntityManager()->getRepository(Role::class)->find($id);
            $this->getEntityManager()->remove($role);
            $this->getEntityManager()->flush();
            return new ResultOperation(true, 'Role deletado com sucesso');
        } catch (Exception $exception) {
            return new ResultOperation(false, $exception->getMessage());
        }
    }


    //    /**
    //     * @return Role[] Returns an array of Role objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Role
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

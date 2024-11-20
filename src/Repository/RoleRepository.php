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


    public function addRole(RoleCreateDto $role): ResultOperation
    {
        if ($role === null) {
            return new ResultOperation(false, 'Role não pode ser nulo');
        }

        try {
            $role = $this->_mapperServiceCreate->mapRole($role);
            $this->getEntityManager()->persist($role);
            $this->getEntityManager()->flush();
            return new ResultOperation(true, 'Role criado com sucesso');
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


    public function getRoleById(string $id): ?RoleResponseDto
    {
        if ($id == null) {
            return null;
        }

        try {
            $role = $this->getEntityManager()->getRepository(Role::class)->find($id);
            $result = $this->_mapperServiceResponse->mapRoleToDto($role);

            if ($result == null) {
                return null;
            }

            return $result;
        } catch (Exception $exception) {
            return null;
        }
    }

    public function getRoles(): ?array
    {
        try {
            $roles = $this->getEntityManager()->getRepository(Role::class)->findAll();
            $roleResponseDtos = [];

            foreach ($roles as $role) {
                $roleResponseDto = $this->_mapperServiceResponse->mapRoleToDto($role);
                $roleResponseDtos[] = $roleResponseDto;
            }
            return $roleResponseDtos;
        } catch (Exception $exception) {
            return null;
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

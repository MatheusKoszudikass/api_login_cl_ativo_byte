<?php

namespace App\Repository;

use App\Util\DoctrineFindParams;
use App\Entity\Enum\TypeEntitiesEnum;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Interface\Repository\BaseRepositoryInterface;
use Doctrine\ORM\Exception\ORMException;
use Exception;

class BaseRepository extends ServiceEntityRepository implements BaseRepositoryInterface
{

    public function __construct(ManagerRegistry $registry, 
    TypeEntitiesEnum $typeEntitiesEnum
    ){
        parent::__construct($registry, $typeEntitiesEnum->value);
    }

    public function createEntity($entity)
    {
        try {
            $this->persist($entity);
            return true;
        } catch (ORMException $ex) {
            throw new Exception('Erro ma criação da entidade: ' . $ex->getMessage());
        }
    }

    public function getEntity(DoctrineFindParams $identifier)
    {
        try {
            return $this->getEntityManager()->find($identifier->getType()->value, $identifier->getIdentifier());
        } catch (ORMException $ex) {
            throw new Exception('Erro ao obter a imagem: ' . $ex->getMessage());
        }
    }

    public function getEntitiesAll(DoctrineFindParams $criteria, $page, $size)
    {
        try {
            $query = $this->getEntityManager()
                ->createQuery('SELECT i FROM ' . $criteria->getType()->value . ' i');
    
            $query->setFirstResult(self::calculateOffset($page, $size));
            $query->setMaxResults($size);
    
            return $query->getResult();
        } catch (ORMException $ex) {
            throw new Exception('Erro ao obter as imagens: ' . $ex->getMessage());
        }
    }
    

    public function getEntityOneBy(DoctrineFindParams $criteria)
    {
        try {
            return $this->getEntityManager()
                ->getRepository($criteria->getType()->value)
                ->findOneBy($criteria->toArrayParams());
        } catch (ORMException $ex) {
            throw new Exception('Erro ao obter a imagem: ' . $ex->getMessage());
        }
    }

    public function getEntitiesBy(DoctrineFindParams $criteria)
    {
        try {
            return $this->getEntityManager()
                ->getRepository($criteria->getType()->value)
                ->findBy($criteria->toArrayParams());
        } catch (ORMException $ex) {
            throw new Exception('Erro ao obter as imagens: ' . $ex->getMessage());
        }
    }

    public function updateEntity($entity, DoctrineFindParams $criteria)
    {
        try {
            $entityBd = self::getEntity($criteria);
            if (!$entityBd) return null;

            $entityBd->setName($entity->name);
            $entityBd->setPath($entity->path);

            $this->persist($entityBd);

            return $entityBd;
        } catch (ORMException $ex) {
            throw new Exception('Erro ao atualizar a imagem: ' . $ex->getMessage());
        }
    }

    public function deleteEntity(DoctrineFindParams $criteria)
    {
        try {
            $entity = self::getEntityOneBy($criteria);
            if ($entity) {
                $this->getEntityManager()->remove($entity);
                $this->getEntityManager()->flush();
                return true;
            }
            return false;
        } catch (ORMException $ex) {
            throw new Exception('Erro ao deletar a imagem: ' . $ex->getMessage());
        }
    }

    public function persist($entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    private function calculateOffset(int $page, int $size): int
    {
        return ($page - 1) * $size;
    }
}


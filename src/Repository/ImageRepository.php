<?php

namespace App\Repository;

use App\Entity\Image;
use App\Interface\ImageRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

class ImageRepository extends ServiceEntityRepository implements ImageRepositoryInterface
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

	public function create($entity): bool
	{
		try 
		{
			$this->getEntityManager()->persist($entity);
			$this->getEntityManager()->flush();
			return true;
			
		} catch (ORMException $ex) 
		{
			throw $ex;
		}
	}

	public function delete($criteria): bool
	{
		try
		{
			$entity = self::getOneBy($criteria);

			if ($entity) {
				$this->getEntityManager()->remove($entity);
				$this->getEntityManager()->flush();
				return true;
			} 
			return false;

		} catch(ORMException $ex)
		{
			throw $ex;
		}
	}

	public function update($entity, $criteria)
	{
	
		try 
		{
			$image = self::getOneBy($criteria);
			if (!$entity) return false;

			$image->setName($entity->name);
			$image->setPath($entity->path);
			
			$this->getEntityManager()->persist($image);
			$this->getEntityManager()->flush();

			return $entity;

		} catch(ORMException $ex)
		{
			throw $ex;
		}
	}

	public function get($id): ?Image
	{
		try
		{			
			return $this->getEntityManager()->find(Image::class, $id);	

		} catch(ORMException $ex)
		{
			throw $ex;
		}
	}

	public function getAll(): ?array
	{
		try
		{
			return $this->getEntityManager()
				->createQuery('SELECT i FROM App\Entity\Image i')
				->getResult();

		} catch(ORMException $ex)
		{
			throw $ex;
		}
	}

	public function getOneBy($criteria): ?Image
	{
		try
		{
			return $this->getEntityManager()->
			getRepository(Image::class)->findOneBy($criteria->toArrayParams());

		} catch(ORMException $ex)
		{
			throw $ex;
		}
	}

	public function getBy($criteria): ?Image
	{
		try
		{
			return $this->getEntityManager()->getRepository(Image::class)->getBy($criteria);

		} catch(ORMException $ex)
		{
			throw $ex;
		}
	}
}
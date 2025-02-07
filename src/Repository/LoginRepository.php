<?php

namespace App\Repository;


use App\Entity\Enum\TypeEntitiesEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Interface\Repository\LoginRepositoryInterface;

class LoginRepository extends ServiceEntityRepository implements LoginRepositoryInterface
{
    
    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, TypeEntitiesEnum::LOGIN->value);
    }
}
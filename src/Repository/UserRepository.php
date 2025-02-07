<?php

namespace App\Repository;

use App\Entity\User;
use App\Interface\Repository\UserRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{

    public function __construct(
        ManagerRegistry $registry,
    ) {
        parent::__construct($registry, User::class);
    }
}

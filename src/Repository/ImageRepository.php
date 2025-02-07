<?php

namespace App\Repository;

use App\Entity\Image;
use App\Interface\Repository\ImageRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ImageRepository extends ServiceEntityRepository implements ImageRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }
    
}

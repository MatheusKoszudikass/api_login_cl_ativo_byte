<?php

declare(strict_types=1);

namespace Doctrine\Common\DataFixtures\Purger;

use Doctrine\ORM\EntityManagerInterface;

interface ORMPurgerInterface extends PurgerInterface
{
    /**
     * Set the EntityManagerInterface instance this purger instance should use.
     */
    public function setEntityManager(EntityManagerInterface $em): void;
}

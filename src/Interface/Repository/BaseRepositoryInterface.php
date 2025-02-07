<?php

namespace App\Interface\Repository;

use App\Util\DoctrineFindParams;
/**
 * @template TEntity
 * @template TPageNumber
 * @template TPageSize
 */
interface BaseRepositoryInterface
{
    /**
     *
     * @param TEntity $entity
     * @return TEntity
     */
    public function createEntity($entity);

    /**
     *
     * @return TEntity|null
     */
    public function getEntity(DoctrineFindParams $criteria);

    /**
     * @param TPageNumber $pageNumber
     * @param TPageSize $pageSize
     * @return TEntity[]
     */
    public function getEntitiesAll(DoctrineFindParams $criteria, $pageNumber, $pageSize);

    /**
     *
     * @return TEntity|null
     */
    public function getEntityOneBy(DoctrineFindParams $criteria);

    /**
     *
     * @return TEntity[]
     */
    public function getEntitiesBy(DoctrineFindParams $criteria);

    /**
     *
     * @param TEntity $entity
     * @return TEntity
     */
    public function updateEntity($entity, DoctrineFindParams $criteria);

    /**
     *
     * @return bool
     */
    public function deleteEntity(DoctrineFindParams $criteria);

    /**
     *
     * @param TEntity $entity
     * @return void
     */
    public function persist($entity);
}

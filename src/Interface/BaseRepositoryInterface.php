<?php

namespace App\Interface;

/**
 * @template T
 * @template TCriteria
 */
interface BaseRepositoryInterface
{
    /**
     * @param T $entity
     * @return T
     */
    public function create($entity);

    /**
     * @param T $id
     * @return T
     */
    public function delete($id);

    /**
     * @param  T
     * @return T|null
     */
    public function get($identifier);

    /**
     * @return T[]
     */
    public function getAll();

    /**
     * @param TCriteria $criteria
     * @return T|null
     */
    public function getOneBy($criteria);

    /**
     * @param TCriteria $criteria
     * @return T|null
     */
    public function getBy($criteria);

    /**
     * @param T $entity
     * @param  TCriteria $criteria
     * @return T
     */
    public function update($entity, $criteria);
}

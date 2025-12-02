<?php

namespace App\Repository;

use App\Interfaces\IBaseRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

abstract class BaseRepository extends EntityRepository implements IBaseRepository
{
    public function create(object $entity): object
    {
        try {
            $this->_em->beginTransaction();

            $this->_em->persist($entity);
            $this->_em->flush();

            $this->_em->commit();
            return $entity;

        } catch (ORMException | OptimisticLockException $e) {
            $this->_em->rollback();
            throw new \Exception("Doctrine error creating entity: " . $e->getMessage(), 0, $e);

        } catch (\Throwable $e) {
            $this->_em->rollback();
            throw new \Exception("Unexpected error creating entity: " . $e->getMessage(), 0, $e);
        }
    }

    public function update(object $entity): object
    {
        try {
            $this->_em->beginTransaction();

            $this->_em->persist($entity);
            $this->_em->flush();

            $this->_em->commit();

            return $entity;

        } catch (ORMException | OptimisticLockException $e) {
            $this->_em->rollback();
            throw new \Exception("Doctrine error updating entity: " . $e->getMessage(), 0, $e);

        } catch (\Throwable $e) {
            $this->_em->rollback();
            throw new \Exception("Unexpected error updating entity: " . $e->getMessage(), 0, $e);
        }
    }

    public function delete(object $entity): bool
    {
        try {
            $this->_em->beginTransaction();

            $this->_em->remove($entity);
            $this->_em->flush();

            $this->_em->commit();
            return true;

        } catch (ORMException | OptimisticLockException $e) {
            $this->_em->rollback();
            throw new \Exception("Doctrine error deleting entity: " . $e->getMessage(), 0, $e);

        } catch (\Throwable $e) {
            $this->_em->rollback();
            throw new \Exception("Unexpected error deleting entity: " . $e->getMessage(), 0, $e);
        }
    }

    public function findById(int|string $id): ?object
    {
        try {
            return $this->_em->find($this->_entityName, $id);

        } catch (ORMException $e) {
            throw new \Exception("Doctrine error finding entity by ID: " . $e->getMessage(), 0, $e);

        } catch (\Throwable $e) {
            throw new \Exception("Unexpected error finding entity by ID: " . $e->getMessage(), 0, $e);
        }
    }

    public function findAll(): array
    {
        try {
            return parent::findAll();

        } catch (ORMException $e) {
            throw new \Exception("Doctrine error fetching all entities: " . $e->getMessage(), 0, $e);

        } catch (\Throwable $e) {
            throw new \Exception("Unexpected error fetching all entities: " . $e->getMessage(), 0, $e);
        }
    }

}

<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

abstract class BaseRepository extends EntityRepository
{
    abstract public function create(object $entity): object;

    abstract public function update(object $entity): object;

    abstract public function delete(object $entity): bool;

    abstract public function findById(int|string $id): ?object;

    abstract public function getAll(): array;
}

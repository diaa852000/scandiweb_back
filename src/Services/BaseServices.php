<?php
namespace App\Services;

abstract class BaseServices
{
    abstract public function create(object $entity): object;

    abstract public function update(object $entity): object;

    abstract public function delete(object $entity): bool;

    abstract public function findById(int|string $id): ?object;

    abstract public function findAll(): array;

}

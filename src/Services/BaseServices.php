<?php
namespace App\Services;

abstract class BaseServices
{
    abstract public function create(array $data): object;

    abstract public function update(int|string $id, array $data): object;

    abstract public function delete(int|string $id): bool;

    abstract public function findById(int|string $id): ?object;

    abstract public function findAll(): array;
}

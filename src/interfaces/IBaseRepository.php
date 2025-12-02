<?php

namespace App\Interfaces;

interface IBaseRepository
{
    public function findAll(): array;

    public function create(object $entity): object;

    public function update(object $entity): object;

    public function delete(object $entity): bool;

    public function findById(int|string $id): ?object;
}
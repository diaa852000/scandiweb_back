<?php

namespace App\Interfaces;

interface IBaseRepository
{
    public function findAll(): array;

    public function create(object $entity): object;

    public function update(object $entity): void;

    public function delete(object $entity): void;

    public function findById(int|string $id): ?object;
}
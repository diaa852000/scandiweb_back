<?php
namespace App\Repository;

use App\Entities\Category;

class CategoryRepository extends BaseRepository
{
    #[\Override]
    public function findAll(): array
    {
        return parent::findAll();
    }

    #[\Override]
    public function findById(int|string $id): ?Category
    {
        return $this->_em->find(Category::class, $id);
    }

    #[\Override]
    public function create(object $entity): object
    {
        if (!$entity instanceof Category) {
            throw new \InvalidArgumentException("Expected Category entity");
        }

        $this->_em->persist($entity);
        $this->_em->flush();

        return $entity;
    }

    #[\Override]
    public function update(object $entity): object
    {
        if (!$entity instanceof Category) {
            throw new \InvalidArgumentException("Expected Category entity");
        }

        $this->_em->persist($entity);
        $this->_em->flush();

        return $entity;
    }

    #[\Override]
    public function delete(object $entity): bool
    {
        if (!$entity instanceof Category) {
            throw new \InvalidArgumentException("Expected Category entity");
        }

        $this->_em->remove($entity);
        $this->_em->flush();

        return true;
    }
}

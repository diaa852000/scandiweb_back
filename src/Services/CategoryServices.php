<?php
namespace App\Services;

use App\Entities\Category;
use App\Repository\CategoryRepository;
use GraphQL\Error\UserError;
use App\Services\BaseServices;

class CategoryServices extends BaseServices
{
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    #[\Override()]
    public function create(object $entity): Category
    {
        $existing = $this->categoryRepository->findById($entity->id);
        if ($existing) {
            throw new UserError("Category with name '{$entity->name}' already exists.");
        }

        $category = new Category();
        $category->id = $entity->id;
        $category->name = $entity->name;

        $this->categoryRepository->create($category);

        return $category;
    }

    #[\Override()]
    public function update(object $entity): Category
    {
        $category = $this->categoryRepository->findById($entity->id);
        if (!$category) {
            throw new \RuntimeException("Category with id '{$entity->id}' not found.");
        }

        $existing = $this->categoryRepository->findById($entity->id);
        if ($existing && $existing->id !== $entity->id) {
            throw new \RuntimeException("Another category with name '{$entity->name}' already exists.");
        }

        $category->name = $entity->name;
        $this->categoryRepository->update($category);

        return $category;
    }

    #[\Override()]
    public function delete(object $entity): bool
    {
        $category = $this->categoryRepository->findById($entity->id);
        if (!$category) {
            throw new \RuntimeException("Category with id '{$entity->id}' not found.");
        }

        return $this->categoryRepository->delete($category);
    }

    #[\Override()]
    public function findAll(): array
    {
        return $this->categoryRepository->findAll();
    }

    #[\Override()]
    public function findById(int|string $id): ?Category
    {
        return $this->categoryRepository->findById($id);
    }
}

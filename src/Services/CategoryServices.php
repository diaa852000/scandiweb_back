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

    public function create(array $data): Category
    {
        $existing = $this->categoryRepository->findById($data['id']);
        if ($existing) {
            throw new UserError("Category with id '{$data['id']}' already exists.");
        }

        $category = new Category();
        $category->id = $data['id'];
        $category->name = $data['name'];

        $this->categoryRepository->create($category);

        return $category;
    }

    public function update(int|string $id, array $data): Category
    {
        $category = $this->categoryRepository->findById($id);
        if (!$category) {
            throw new \RuntimeException("Category with id '{$id}' not found.");
        }

        if (!isset($data['name'])) {
            throw new \RuntimeException("Category name is required.");
        }

        $category->name = $data['name'];
        $this->categoryRepository->update($category);

        return $category;
    }

    public function delete(int|string $id): bool
    {
        $category = $this->categoryRepository->findById($id);
        if (!$category) {
            throw new \RuntimeException("Category with id '{$id}' not found.");
        }

        return $this->categoryRepository->delete($category);
    }

    public function findAll(): array
    {
        return $this->categoryRepository->findAll();
    }

    public function findById(int|string $id): ?Category
    {
        return $this->categoryRepository->findById($id);
    }
}

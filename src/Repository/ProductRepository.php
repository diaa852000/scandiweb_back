<?php
namespace App\Repository;
use App\Repository\BaseRepository;

class ProductRepository extends BaseRepository
{
    public function findProductsByCategory(string $category_id): array
    {
        return $this->findBy(['category' => $category_id]);
    }
}

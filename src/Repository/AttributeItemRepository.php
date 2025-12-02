<?php
namespace App\Repository;

use App\Entities\Attribute;
use App\Repository\BaseRepository;

class AttributeItemRepository extends BaseRepository
{
    public function findByAttribute(Attribute $attribute): array
    {
        try {
            return $this->createQueryBuilder('i')
                ->where('i.attribute = :attr')
                ->setParameter('attr', $attribute)
                ->orderBy('i.id', 'ASC')
                ->getQuery()
                ->getResult();
        } catch (\Throwable $e) {
            throw new \Exception(
                "Error fetching AttributeItems for Attribute ID {$attribute->id}: " . $e->getMessage(),
                0,
                $e
            );
        }
    }
}

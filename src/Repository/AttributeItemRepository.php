<?php
namespace App\Repository;

use App\Entities\Attribute;
use App\Entities\AttributeItem;
use Doctrine\ORM\EntityRepository;

class AttributeItemRepository extends EntityRepository
{

    public function findByAttribute(Attribute $attribute): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.attribute = :attr')
            ->setParameter('attr', $attribute)
            ->getQuery()
            ->getResult();
    }
}

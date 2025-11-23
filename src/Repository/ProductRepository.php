<?php
namespace App\Repository;

use App\Entities\Product;
use App\Repository\BaseRepository;

class ProductRepository extends BaseRepository
{
    public function getAll(): array
    {
        return $this->findAll();
    }

    public function findById(int|string $id): ?Product
    {
        return $this->find($id);
    }

    #[\Override()]
    public function create(object $entity): object
    {

        $this->_em->beginTransaction();
        try {
            $this->_em->persist($entity);
            $this->_em->flush();
            $this->_em->commit();
            $this->_em->refresh($entity);
            return $entity;
        } catch (\Exception $e) {
            $this->_em->rollback();
            throw $e;
        }
    }

    #[\Override()]
    public function delete(object $object): bool
    {
        $this->_em->beginTransaction();
        try {
            $product = $this->findById($object->id);
            if (!$product) {
                throw new \Exception("Product with id '{$object->id}' not found.");
            }

            $this->_em->remove($product);
            $this->_em->flush();
            $this->_em->commit();
            return true;
        } catch (\Exception $e) {
            $this->_em->rollback();
            throw $e;
        }
    }

    #[\Override()]
    public function update(object $object): object
    {
        $this->_em->beginTransaction();
        try {
            $product = $this->findById($object->id);
            if (!$product) {
                throw new \Exception("Product with id '{$object->id}' not found.");
            }

            $product->name = $object->name;
            $product->in_stock = $object->in_stock;
            $product->description = $object->description;
            $product->brand = $object->brand;
            $product->price = $object->price;
            $product->gallery = $object->gallery;
            $product->attributes = $object->attributes;

            $this->_em->flush();
            $this->_em->commit();
            return $object;
        } catch (\Exception $e) {
            $this->_em->rollback();
            throw $e;
        }
    }

    public function findProductsByCategory(string $category_id): array
    {
        $products = $this->findBy(['category' => $category_id]);
        return count($products) > 0 ? $products : [];
    }
}

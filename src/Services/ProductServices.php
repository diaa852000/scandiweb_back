<?php

namespace App\Services;

use App\Entities\Product;
use App\Entities\Gallery;
use App\Entities\Price;
use App\Entities\Category;
use App\Entities\Attribute;
use App\Entities\AttributeItem;
use App\Repository\ProductRepository;
use App\Services\GalleryServices;
use App\Services\PriceServices;
use App\Services\CategoryServices;
use App\Services\AttributeServices;
use Doctrine\ORM\EntityManagerInterface;
use GraphQL\Error\UserError;
use Doctrine\Common\Collections\ArrayCollection;
use App\Services\BaseServices;

class ProductServices extends BaseServices
{
    private ProductRepository $productRepository;
    private EntityManagerInterface $em;
    private GalleryServices $galleryServices;
    private PriceServices $priceServices;
    private CategoryServices $categoryServices;
    private AttributeServices $attributeServices;


    public function __construct(
        ProductRepository $productRepository,
        CategoryServices $categoryServices,
        AttributeServices $attributeServices,
        GalleryServices $galleryServices,
        PriceServices $priceServices,
        EntityManagerInterface $em
    ) {
        $this->productRepository = $productRepository;
        $this->categoryServices = $categoryServices;
        $this->attributeServices = $attributeServices;
        $this->galleryServices = $galleryServices;
        $this->priceServices = $priceServices;
        $this->em = $em;
    }

    public function findAll(): array
    {
        return $this->productRepository->findAll();
    }

    public function findById(int|string $id): object|null
    {
        return $this->productRepository->findById($id);
    }


    public function create(object $entity): Product
    {
        $existing = $this->productRepository->findById($entity->id);
        if ($existing) {
            throw new UserError("Product with id '{$entity->id}' already exists.");
        }

        $product = new Product();
        $product->id = $entity->id;
        $product->name = $entity->name;
        $product->in_stock = $entity->in_stock;
        $product->description = $entity->description;
        $product->brand = $entity->brand;

        if ($entity->category !== null) {
            $category = $this->categoryServices->findCategory($entity->category_Id);
            if (!$category) {
                throw new UserError("Category with id '{$entity->category_Id}' not found.");
            }
            $product->category = $category;
        }

        $product->gallery = new ArrayCollection();
        foreach ($entity->gallery as $url) {
            $this->galleryServices->addGalleryItem($product, $url);
        }

        foreach ($entity->prices as $p) {
            if (!isset($p['amount'], $p['currency']['label'], $p['currency']['symbol'])) {
                throw new UserError("Invalid price input. Expected { amount, currency: { label, symbol } }");
            }
            $this->priceServices->addPrice(
                $product,
                (float) $p['amount'],
                $p['currency']['label'],
                $p['currency']['symbol']
            );
        }

        foreach ($entity->attributes ?? [] as $set) {
            if (!isset($set['id'])) {
                throw new UserError("Each attribute must have an 'id'.");
            }
            $name = $set['name'] ?? $set['id'];
            $type = $set['type'] ?? 'text';
            $items = $set['items'] ?? [];

            $attr = $this->attributeServices->upsertAttributeWithItemsNoFlush(
                $set['id'],
                $name,
                $type,
                $items
            );

            $product->addAttribute($attr);
        }

        return $this->productRepository->create($product);
    }


    public function update(object $entity): Product
    {
        $product = $this->productRepository->findProductById($entity->id);
        if (!$product) {
            throw new UserError("Product with id '{$entity->id}' not found.");
        }

        if ($entity->name !== null)
            $product->name = $entity->name;
        if ($entity->inStock !== null)
            $product->in_stock = $entity->inStock;
        if ($entity->description !== null)
            $product->description = $entity->description;
        if ($entity->brand !== null)
            $product->brand = $entity->brand;

        if ($entity->category !== null) {
            $category = $this->categoryServices->findById($entity->category);
            if (!$category) {
                throw new UserError("Category with id '{$entity->category}' not found.");
            }
            $product->category = $category;
        }

        $this->productRepository->update($product);
        return $product;
    }

    public function delete(object $entity): bool
    {
        $product = $this->productRepository->findById($entity->id);
        if (!$product) {
            return false;
        }

        $this->productRepository->delete($product);
        $this->em->flush();
        return true;
    }

    public function getProductsByCategory(string $category_id): array
    {
        return $this->productRepository->findProductsByCategory($category_id);
    }

}
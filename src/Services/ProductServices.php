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

    // public function create(array $data): Product
    // {
    //     $existing = $this->productRepository->findById($data['id']);
    //     if ($existing) {
    //         throw new UserError("Product with id '{$data['id']}' already exists.");
    //     }

    //     $product = new Product();
    //     $product->id = $data['id'];
    //     $product->name = $data['name'];
    //     $product->in_stock = $data['in_stock'];
    //     $product->description = $data['description'];
    //     $product->brand = $data['brand'];

    //     if ($data['category_id'] !== null) {
    //         $category = $this->categoryServices->findById($data['category_id']);
    //         if (!$category) {
    //             throw new UserError("Category with id '{$data['category_Id']}' not found.");
    //         }
    //         $product->category = $category;
    //     }

    //     $product->gallery = new ArrayCollection();
    //     foreach ($data['gallery'] as $url) {
    //         $this->galleryServices->addGalleryItem($product, $url);
    //     }

    //     foreach ($data['prices'] as $p) {
    //         if (!isset($p['amount'], $p['currency']['label'], $p['currency']['symbol'])) {
    //             throw new UserError("Invalid price input. Expected { amount, currency: { label, symbol } }");
    //         }
    //         $this->priceServices->addPrice(
    //             $product,
    //             (float) $p['amount'],
    //             $p['currency']['label'],
    //             $p['currency']['symbol']
    //         );
    //     }

    //     foreach ($entity->attributes ?? [] as $set) {
    //         if (!isset($set['id'])) {
    //             throw new UserError("Each attribute must have an 'id'.");
    //         }
    //         $name = $set['name'] ?? $set['id'];
    //         $type = $set['type'] ?? 'text';
    //         $items = $set['items'] ?? [];

    //         $attr = $this->attributeServices->upsertAttributeWithItemsNoFlush(
    //             $set['id'],
    //             $name,
    //             $type,
    //             $items
    //         );

    //         $product->addAttribute($attr);
    //     }

    //     return $this->productRepository->create($product);
    // }


    public function create(array $data): Product
    {
        $existing = $this->productRepository->findById($data['id']);
        if ($existing) {
            throw new UserError("Product with id '{$data['id']}' already exists.");
        }

        $product = new Product();
        $product->id = $data['id'];
        $product->name = $data['name'];
        $product->in_stock = $data['in_stock'];
        $product->description = $data['description'];
        $product->brand = $data['brand'];

        // Category
        if ($data['category_id'] !== null) {
            $category = $this->categoryServices->findById($data['category_id']);
            if (!$category) {
                throw new UserError("Category with id '{$data['category_id']}' not found.");
            }
            $product->category = $category;
        }

        // GALLERY
        foreach ($data['gallery'] ?? [] as $url) {
            $this->galleryServices->addGalleryItem($product, $url);
        }

        // PRICES
        foreach ($data['prices'] ?? [] as $p) {
            $this->priceServices->addPrice(
                $product,
                (float) $p['amount'],
                $p['currency']['label'],
                $p['currency']['symbol']
            );
        }

        // ATTRIBUTES (FIXED VARIABLE NAME)
        foreach ($data['attributes'] ?? [] as $set) {
            $attr = $this->attributeServices->upsertAttributeWithItemsNoFlush(
                $set['id'],
                $set['name'] ?? $set['id'],
                $set['type'] ?? 'text',
                $set['items'] ?? []
            );

            $product->addAttribute($attr);
        }

        return $this->productRepository->create($product);
    }

    public function update(int|string $id, array $data): Product
    {
        $product = $this->productRepository->findById($id);
        if (!$product) {
            throw new UserError("Product with id '{$id}' not found.");
        }

        if ($data['name'] !== null)
            $product->name = $data['name'];
        if ($data['inStock'] !== null)
            $product->in_stock = $data['in_stock'];
        if ($data['description'] !== null)
            $product->description = $data['description'];
        if ($data['brand'] !== null)
            $product->brand = $data['brand'];

        if ($data['category'] !== null) {
            $category = $this->categoryServices->findById($data['category']);
            if (!$category) {
                throw new UserError("Category with id '{$data['category']}' not found.");
            }
            $product->category = $category;
        }

        $this->productRepository->update($product);
        return $product;
    }

    public function delete(int|string $id): bool
    {
        $product = $this->productRepository->findById($id);
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
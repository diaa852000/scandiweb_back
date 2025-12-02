<?php

namespace App\GraphQL\Schema;

use App\Entities\Currency;
use App\GraphQL\Query\CategoryQuery;
use App\GraphQL\Query\ProductQuery;
use App\GraphQL\Mutation\CategoryMutation;
use App\GraphQL\Mutation\ProductMutation;
use App\GraphQL\Mutation\OrderMutation;

use App\Entities\Category;
use App\Entities\Attribute;
use App\Entities\Price;
use App\Entities\Gallery;
use App\Entities\AttributeItem;
use App\Entities\Product;
use App\Entities\Order;
use App\Entities\OrderItem;

use App\Repository\AttributeItemRepository;
use App\Repository\AttributeRepository;
use App\Repository\CategoryRepository;
use App\Repository\CurrencyRepository;
use App\Repository\GalleryRepository;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Repository\PriceRepository;
use App\Repository\ProductRepository;
use App\Services\CategoryServices;
use App\Services\AttributeServices;
use App\Services\GalleryServices;
use App\Services\PriceServices;
use App\Services\ProductServices;
use App\Services\OrderServices;

use Doctrine\ORM\EntityManagerInterface;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;

class SchemaFactory
{
    public static function build(EntityManagerInterface $em): Schema
    {
        // $categoryRepo = $em->getRepository(Category::class);
        $categoryRepo = new CategoryRepository($em, $em->getClassMetadata(Category::class));
        $categoryService = new CategoryServices($categoryRepo);


        // $attributeItemRepo = $em->getRepository(AttributeItem::class);
        // $attributeRepo = $em->getRepository(Attribute::class);

        $attributeRepo = new AttributeRepository($em, $em->getClassMetadata(Attribute::class));
        $attributeItemRepo = new AttributeItemRepository($em, $em->getClassMetadata(AttributeItem::class));
        $attributeService = new AttributeServices($attributeRepo, $attributeItemRepo, $em);


        // $galleryRepo = $em->getRepository(Gallery::class);
        $galleryRepo = new GalleryRepository($em, $em->getClassMetadata(Gallery::class));
        $galleryService = new GalleryServices($galleryRepo);

        // $priceRepo = $em->getRepository(Price::class);
        $priceRepo = new PriceRepository($em, $em->getClassMetadata(Price::class));
        $currencyRepo = new CurrencyRepository($em, $em->getClassMetadata(Currency::class));
        $priceSerivce = new PriceServices($priceRepo, $currencyRepo, $em);

        $productRepo = new ProductRepository($em, $em->getClassMetadata(Product::class));
        $productService = new ProductServices(
            $productRepo,
            $categoryService,
            $attributeService,
            $galleryService,
            $priceSerivce,
            $em
        );


        $orderRepo = new OrderRepository($em, $em->getClassMetadata(Order::class));
        $orderItemRepo = new OrderItemRepository($em, $em->getClassMetadata(OrderItem::class));
        $orderService = new OrderServices(
            $em,
            $orderRepo,
            $orderItemRepo,
            $productRepo
        );

        $categoryQuery = new CategoryQuery($categoryService);
        $productQuery = new ProductQuery($productService);

        $categoryMutation = new CategoryMutation($categoryService);
        $productMutation = new ProductMutation($productService);
        $orderMutation = new OrderMutation($orderService);

        $queryType = new ObjectType([
            'name' => 'Query',
            'fields' => fn() => array_merge(
                $categoryQuery->getFields(),
                $productQuery->getFields()
            ),
        ]);

        $mutationType = new ObjectType([
            'name' => 'Mutation',
            'fields' => fn() => array_merge(
                $categoryMutation->getFields(),
                $productMutation->getFields(),
                $orderMutation->getFields()
            ),
        ]);

        return new Schema(
            (new SchemaConfig())
                ->setQuery($queryType)
                ->setMutation($mutationType)
        );
    }
}

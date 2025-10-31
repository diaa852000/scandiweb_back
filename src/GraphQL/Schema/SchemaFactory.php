<?php

namespace App\GraphQL\Schema;

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
        $categoryRepo = $em->getRepository(Category::class);
        $categoryService = new CategoryServices($categoryRepo);


        $attributeItemRepo = $em->getRepository(AttributeItem::class);
        $attributeRepo = $em->getRepository(Attribute::class);
        $attributeService = new AttributeServices($attributeRepo,$attributeItemRepo, $em);

        $galleryRepo = $em->getRepository(Gallery::class);
        $galleryService = new GalleryServices($galleryRepo);

        $priceRepo = $em->getRepository(Price::class);
        $priceSerivce = new PriceServices($priceRepo, $em);

        $productRepo = $em->getRepository(Product::class);
        $productService = new ProductServices(
            $productRepo,
            $categoryService,
            $attributeService,
            $galleryService,
            $priceSerivce,
            $em
        );


        $orderRepo = $em->getRepository(Order::class);
        $orderItemRepo = $em->getRepository(OrderItem::class);
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

<?php

namespace App\GraphQL\Query;

use App\Services\ProductServices;
use App\GraphQL\Types\TypeRegistry;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class ProductQuery extends ObjectType
{
    public function __construct(ProductServices $productService)
    {
        $config = [
            'name' => 'ProductQuery',
            'fields' => [
                'products' => [
                    'type' => Type::listOf(TypeRegistry::product()),
                    'resolve' => fn() => $productService->getAllProducts(),
                ],

                'product' => [
                    'type' => TypeRegistry::product(),
                    'args' => [
                        'id' => Type::nonNull(Type::string()),
                    ],
                    'resolve' => fn($root, $args) =>
                        $productService->findOneProduct($args['id']),
                ],

                'productsByCategory' => [
                    'type' => Type::listOf(TypeRegistry::product()),
                    'args' => [
                        'category_id' => Type::nonNull(Type::string()),
                    ],
                    'resolve' => fn($root, $args) =>
                        $args['category_id'] === 'all'
                            ? $productService->getAllProducts()
                            : $productService->getProductsByCategory($args['category_id']),
                ],
            ],
        ];

        parent::__construct($config);
    }
}

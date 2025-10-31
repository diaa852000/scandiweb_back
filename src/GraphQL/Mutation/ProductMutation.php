<?php

namespace App\GraphQL\Mutation;

use App\Services\ProductServices;
use App\GraphQL\Types\TypeRegistry;
use App\Utilities\GraphQLSchemas;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\InputObjectType;

class ProductMutation extends ObjectType
{
    public function __construct(ProductServices $productService)
    {
        $attributeItemInput = new InputObjectType([
            'name' => 'AttributeItemInput',
            'fields' => [
                'id' => Type::nonNull(Type::string()),
                'value' => Type::nonNull(Type::string()),
                'displayValue' => Type::nonNull(Type::string()),
            ],
        ]);

        $attributeSetInput = new InputObjectType([
            'name' => 'AttributeSetInput',
            'fields' => [
                'id' => Type::nonNull(Type::string()),
                'name' => Type::string(),
                'type' => Type::string(),
                'items' => Type::nonNull(Type::listOf(Type::nonNull($attributeItemInput))),
            ],
        ]);

        $config = [
            'name' => 'ProductMutation',
            'fields' => [
                'createProduct' => [
                    'type' => TypeRegistry::product(),
                    'args' => [
                        'id' => Type::nonNull(Type::string()),
                        'name' => Type::nonNull(Type::string()),
                        'in_stock' => Type::nonNull(Type::boolean()),
                        'description' => Type::string(),
                        'brand' => Type::string(),
                        'category_id' => Type::string(),
                        'gallery' => Type::listOf(Type::string()),
                        'prices' => Type::listOf(TypeRegistry::priceInput()),
                        'attributes' => Type::listOf(Type::nonNull($attributeSetInput)),
                    ],
                    'resolve' => fn($root, $args) => $productService->createProduct(
                        $args['id'],
                        $args['name'],
                        $args['in_stock'],
                        $args['description'] ?? null,
                        $args['brand'] ?? null,
                        $args['category_id'] ?? null,
                        $args['gallery'] ?? [],
                        $args['prices'] ?? [],
                        $args['attributes'] ?? []
                    ),
                ],

                'updateProduct' => [
                    'type' => TypeRegistry::product(),
                    'args' => [
                        'id' => Type::nonNull(Type::string()),
                        'name' => Type::string(),
                        'in_stock' => Type::boolean(),
                        'description' => Type::string(),
                        'brand' => Type::string(),
                        'category_id' => Type::string(),
                        'gallery' => Type::listOf(Type::string()),
                        'prices' => Type::listOf(TypeRegistry::priceInput()),
                        'attributes' => Type::listOf(Type::nonNull($attributeSetInput)),
                    ],
                    'resolve' => fn($root, $args) => $productService->updateProduct($args),
                ],

                'deleteProduct' => [
                    'type' => Type::boolean(),
                    'args' => [
                        'id' => Type::nonNull(Type::string()),
                    ],
                    'resolve' => fn($root, $args) => $productService->deleteProduct($args['id']),
                ],
            ],
        ];

        parent::__construct($config);
    }
}

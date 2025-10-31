<?php

namespace App\GraphQL\Types;

use App\Entities\Product;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\TypeRegistry;

class ProductType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Product',
            'fields' => fn() => [
                'id' => Type::nonNull(Type::string()),
                'name' => Type::nonNull(Type::string()),
                'in_stock' => Type::nonNull(Type::boolean()),
                'description' => Type::string(),
                'brand' => Type::string(),
                'category_id' => [
                    'type' => Type::string(),
                    'resolve' => fn(Product $p) => $p->category->id,
                ],
                'gallery' => [
                    'type' => Type::listOf(Type::string()),
                    'resolve' => fn(Product $p) =>
                        array_map(fn($g) => $g->image_url, $p->gallery->toArray()),
                ],
                'prices' => [
                    'type' => Type::listOf(TypeRegistry::price()),
                    'resolve' => fn(Product $p) => $p->prices->toArray(),
                ],
                'attributes' => [
                    'type' => Type::listOf(TypeRegistry::attributeSet()),
                    'resolve' => fn(Product $p) => $p->attributes->toArray(),
                ],
            ],
        ];

        parent::__construct($config);
    }
}

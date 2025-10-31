<?php

namespace App\GraphQL\Query;

use App\GraphQL\Types\CategoryType;
use App\Services\CategoryServices;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\TypeRegistry;

class CategoryQuery extends ObjectType
{
    public function __construct(CategoryServices $categoryService)
    {
        $config = [
            'name' => 'CategoryQuery',
            'fields' => [
                'categories' => [
                    'type' => Type::listOf(TypeRegistry::category()),
                    'resolve' => fn() => $categoryService->getAllCategories(),
                ],
                'category' => [
                    'type' => TypeRegistry::category(),
                    'args' => [
                        'id' => Type::nonNull(Type::string()),
                        'name' => Type::string(),
                    ],
                    'resolve' => fn($root, $args) => $categoryService->findCategory($args['id']),
                ],
            ],
        ];

        parent::__construct($config);
    }
}
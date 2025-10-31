<?php

namespace App\GraphQL\Mutation;

use App\GraphQL\Types\CategoryType;
use App\Repository\CategoryRepository;
use App\Services\CategoryServices;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\TypeRegistry;

class CategoryMutation extends ObjectType
{
    public function __construct(CategoryServices $categoryService)
    {
        $config = [
            'name' => 'CategoryMutation',
            'fields' => [
                'createCategory' => [
                    'type' => TypeRegistry::category(),
                    'args' => [
                        'id' => Type::nonNull(Type::string()),
                        'name' => Type::nonNull(Type::string()),
                    ],
                    'resolve' => fn($root, $args) => $categoryService->createCategory($args['id'], $args['name']),
                ],
                'updateCategory' => [
                    'type' => TypeRegistry::category(),
                    'args' => [
                        'id' => Type::nonNull(Type::string()),
                        'name' => Type::nonNull(Type::string()),
                    ],
                    'resolve' => fn($root, $args) => $categoryService->updateCategory($args['id'], $args['name']),
                ],
                'deleteCategory' => [
                    'type' => Type::boolean(),
                    'args' => [
                        'id' => Type::nonNull(Type::string()),
                    ],
                    'resolve' => fn($root, $args) => $categoryService->deleteCategory($args['id']),
                ],
            ],
        ];

        parent::__construct($config);
    }
}
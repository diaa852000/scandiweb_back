<?php

namespace App\GraphQL\Mutation;

use App\Entities\Category;
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
                        'category' => Type::nonNull(TypeRegistry::categoryInput()),
                    ],
                    'resolve' => fn($root, $args) =>
                        $categoryService->create(
                            // (function () use ($args) {
                            //     $cat = new Category();
                            //     $cat->id = $args['category']['id'];
                            //     $cat->name = $args['category']['name'];
                            //     return $cat;
                            // })()
                            [$args["id"], $args["name"]],
                        ),

                ],
                'updateCategory' => [
                    'type' => TypeRegistry::category(),
                    'args' => [
                        'category' => Type::nonNull(TypeRegistry::categoryInput()),
                    ],
                    'resolve' => fn($root, $args) => $categoryService->update(
                        $args['id'],
                        [$args["id"], $args["name"]],
                    ),
                ],
                'deleteCategory' => [
                    'type' => Type::boolean(),
                    'args' => [
                        'category' => Type::nonNull(TypeRegistry::categoryInput()),
                    ],
                    'resolve' => fn($root, $args) => $categoryService->delete(
                        $args['id'],
                    ),
                ],
            ],
        ];

        parent::__construct($config);
    }
}
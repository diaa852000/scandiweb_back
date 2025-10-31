<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CategoryType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Category',
            'fields' => fn() => [
                'id' => Type::nonNull(Type::string()),
                'name' => Type::string(),
            ],
        ];

        parent::__construct($config);
    }
}
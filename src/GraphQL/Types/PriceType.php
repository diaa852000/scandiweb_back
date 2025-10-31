<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\TypeRegistry;

class PriceType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Price',
            'fields' => fn() => [
                'amount' => Type::nonNull(Type::float()),
                'currency' => TypeRegistry::currency(),
            ],
        ];

        parent::__construct($config);
    }
}

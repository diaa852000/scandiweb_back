<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;


class CurrencyType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Currency',
            'fields' => fn() => [
                'label' => Type::nonNull(Type::string()),
                'symbol' => Type::nonNull(Type::string())
            ]
        ];

        parent::__construct($config);
    }
}
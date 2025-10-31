<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class AttributeItemType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'AttributeItem',
            'fields' => fn() => [
                'id' => Type::nonNull(Type::string()),
                'value' => Type::nonNull(Type::string()),
                'displayValue' => Type::nonNull(Type::string()),
            ],
        ];

        parent::__construct($config);
    }
}

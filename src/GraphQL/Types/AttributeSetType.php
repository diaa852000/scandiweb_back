<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\TypeRegistry;

class AttributeSetType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'AttributeSet',
            'fields' =>  fn() => [
                'id' => Type::nonNull(Type::string()),
                'name' => Type::nonNull(Type::string()),
                'type' => Type::nonNull(Type::string()),
                'items' => Type::listOf(TypeRegistry::attributeItem()),
            ],
        ];

        parent::__construct($config);
    }
}

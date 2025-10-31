<?php

namespace App\GraphQL\Mutation;

use App\Services\OrderServices;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\TypeRegistry;
use App\Utilities\GraphQLSchemas;

class OrderMutation extends ObjectType
{
    public function __construct(OrderServices $orderServices)
    {
        $config = [
            'name' => 'OrderMutation',
            'fields' => [
                'createOrder' => [
                    'type' => TypeRegistry::order(),
                    'args' => [
                            'items' => Type::nonNull(Type::listOf(GraphQLSchemas::orderItemsInputType())),
                        ],
                    'resolve' => function ($root, $args) use ($orderServices) {
                        return $orderServices->placeOrder($args['items']);
                    },
                ]
            ]
        ];
        parent::__construct($config);
    }
}
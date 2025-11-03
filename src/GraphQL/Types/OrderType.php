<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class OrderType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Order',
            'fields' => fn() => [
                    'id' => Type::nonNull(Type::int()),
                    'total' => Type::float(),
                    'orderItems' => [
                        'type' => Type::listOf(Type::string()),
                        'resolve' => function ($order) {
                            $items = $order->orderItems;
                            if (!$items || count($items) === 0) {
                                return [];
                            }

                            $productNames = [];
                            foreach ($items as $item) {
                                $product = $item->product;
                                if ($product) {
                                    $productNames[] = $product->name;
                                }
                            }

                            return $productNames;
                        },
                    ],
                    'created_at' => [
                    'type' => Type::string(),
                    'resolve' => fn($order) => $order->created_at->format('Y-m-d H:i:s'),
                ],
            ]
        ];
        parent::__construct($config);
    }
}
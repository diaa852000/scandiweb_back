<?php
namespace App\Utilities;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InputObjectType;

class GraphQLSchemas
{
    public static function currencyInput(): InputObjectType {
        return new InputObjectType([
            'name' => 'CurrencyInput',
            'fields' => [
                'label' => Type::nonNull(Type::string()),
                'symbol' => Type::nonNull(Type::string()),
            ],
        ]);
    }

    public static function priceInput(): InputObjectType {
        return new InputObjectType([
            'name' => 'PriceInput',
            'fields' => [
                'amount' => Type::nonNull(Type::float()),
                'currency' => self::currencyInput(),
            ],
        ]);
    }

    public static function orderItemsInputType(): InputObjectType
    {
        return new InputObjectType([
            'name' => 'OrderItemInput',
            'fields' => [
                'product_id' => Type::nonNull(Type::string()),
                'quantity' => Type::nonNull(Type::int()),
                'price' => Type::nonNull(Type::float()),
            ]
        ]);
    }
}

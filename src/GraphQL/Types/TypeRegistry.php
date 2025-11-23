<?php

namespace App\GraphQL\Types;

use App\GraphQL\Types\CategoryType;
use App\GraphQL\Types\ProductType;
use App\GraphQL\Types\PriceType;
use App\GraphQL\Types\CurrencyType;
use App\GraphQL\Types\OrderType;
use App\GraphQL\Types\AttributeSetType;
use App\GraphQL\Types\AttributeItemType;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class TypeRegistry
{
    private static array $types = [];

    public static function category(): CategoryType
    {
        return self::$types['Category'] ??= new CategoryType();
    }

    public static function product(): ProductType
    {
        return self::$types['Product'] ??= new ProductType();
    }

    public static function price(): PriceType
    {
        return self::$types['Price'] ??= new PriceType();
    }

    public static function currency(): CurrencyType
    {
        return self::$types['Currency'] ??= new CurrencyType();
    }

    public static function attributeSet(): AttributeSetType
    {
        return self::$types['AttributeSet'] ??= new AttributeSetType();
    }

    public static function attributeItem(): AttributeItemType
    {
        return self::$types['AttributeItem'] ??= new AttributeItemType();
    }

    public static function order(): OrderType
    {
        return self::$types['Order'] ??= new OrderType();
    }


    public static function currencyInput(): InputObjectType
    {
        return self::$types['CurrencyInput'] ??= new InputObjectType([
            'name' => 'CurrencyInput',
            'fields' => [
                'label' => Type::nonNull(Type::string()),
                'symbol' => Type::nonNull(Type::string()),
            ],
        ]);
    }

    public static function priceInput(): InputObjectType
    {
        return self::$types['PriceInput'] ??= new InputObjectType([
            'name' => 'PriceInput',
            'fields' => [
                'amount' => Type::nonNull(Type::float()),
                'currency' => self::currencyInput(),
            ],
        ]);
    }

    public static function categoryInput(): InputObjectType
    {
        return self::$types['CategoryInput'] ??= new InputObjectType([
            'name' => 'CategoryInput',
            'fields' => [
                'id' => Type::nonNull(Type::string()),
                'name' => Type::nonNull(Type::string()),
            ],
        ]);
    }

}

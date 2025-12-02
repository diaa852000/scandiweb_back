<?php

namespace App\Repository;

use App\Entities\OrderItem;
use App\Entities\Order;
use App\Entities\Product;
use App\Repository\BaseRepository;

class OrderItemRepository extends BaseRepository
{
    public function findOrCreate(Order $order, Product $product, int $quantity, float $price): OrderItem
    {
        $item = $this->findOneBy(['order' => $order, 'product' => $product]);
        if ($item) {
            $item->quantity += $quantity;
            return $item;
        }

        $item = new OrderItem();
        $item->order = $order;
        $item->product = $product;
        $item->quantity = $quantity;
        $item->price = $price;

        $em = $this->getEntityManager();
        $em->persist($item);

        $order->addOrderItem($item);

        return $item;
    }

}

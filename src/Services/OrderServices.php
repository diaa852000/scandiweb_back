<?php

namespace App\Services;

use App\Entities\Order;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OrderRepository;
use App\Repository\OrderItemRepository;
use App\Repository\ProductRepository;
use GraphQL\Error\UserError;
use App\Services\BaseServices;


class OrderServices extends BaseServices
{
    public function __construct(
        private EntityManagerInterface $em,
        private OrderRepository $orderRepo,
        private OrderItemRepository $orderItemRepo,
        private ProductRepository $productRepo
    ) {
    }

    public function create(array $cartItems): Order
    {
        $order = new Order();
        $this->orderRepo->create($order);

        $total = 0;

        foreach ($cartItems as $cartItem) {
            $product = $this->productRepo->findById($cartItem['product_id']);
            if (!$product) {
                throw new UserError("Product {$cartItem['product_id']} not found.");
            }

            $this->orderItemRepo->findOrCreate(
                $order,
                $product,
                $cartItem['quantity'],
                $cartItem['price']
            );

            $total += $cartItem['quantity'] * $cartItem['price'];
        }

        $order->total = $total;

        return $this->orderRepo->update($order);
    }


    public function findAll(): array
    {
        return $this->orderRepo->findAll();
    }

    public function findById(int|string $id): object|null
    {
        return $this->orderRepo->findById($id);
    }

    public function delete(int|string $id): bool
    {
        $order = $this->orderRepo->findById($id);
        if (!$order) {
            return false;
        }

        return $this->orderRepo->delete($order);
    }

    public function update(int|string $id, array $data): object
    {
        $order = $this->orderRepo->findById($id);
        if (!$order) {
            throw new \Exception("Order with ID {$id} not found for update");
        }

        if (isset($data['total'])) {
            $order->total = (float) $data['total'];
        }

        if (isset($data['items']) && \is_array($data['items'])) {

            $existing = [];
            foreach ($order->orderItems as $item) {
                $existing[$item->id] = $item;
            }

            foreach ($data['items'] as $i) {

                if (!isset($i['id'])) {
                    continue;
                }

                if (isset($existing[$i['id']])) {
                    $item = $existing[$i['id']];

                    if (isset($i['product_name'])) {
                        $item->product_name = $i['product_name'];
                    }
                    if (isset($i['price'])) {
                        $item->price = (float) $i['price'];
                    }
                    if (isset($i['quantity'])) {
                        $item->quantity = (int) $i['quantity'];
                    }
                }
            }
        }

        return $this->orderRepo->update($order);
    }

}

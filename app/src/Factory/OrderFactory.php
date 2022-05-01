<?php

namespace App\Factory;

use App\Entity\Orders;
use App\Entity\OrderItem;
use App\Entity\Product;

/**
 * Class OrderFactory
 * @package App\Factory
 */
class OrderFactory
{
    /**
     * Creates an order.
     *
     * @return Orders
     */
    public function create(): Orders
    {
        $order = new Orders();
        $order
            ->setStatus(Orders::STATUS_CART)
            ->setCreatedAt(new \DateTime());

        return $order;
    }

    /**
     * Creates an item for a product.
     *
     * @param Product $product
     *
     * @return OrderItem
     */
    public function createItem(Product $product): OrderItem
    {
        $item = new OrderItem();
        $item->setProduct($product);
        $item->setQuantity(1);

        return $item;
    }
}
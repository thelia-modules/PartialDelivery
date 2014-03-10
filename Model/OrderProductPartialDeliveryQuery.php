<?php

namespace PartialDelivery\Model;

use PartialDelivery\Model\Base\OrderProductPartialDeliveryQuery as BaseOrderProductPartialDeliveryQuery;
use Propel\Runtime\ActiveQuery\Criteria;
use Thelia\Model\OrderProduct;
use Thelia\Model\OrderProductQuery;

use Thelia\Model\Order;

/**
 * Skeleton subclass for performing query and update operations on the 'order_product_partial_delivery' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class OrderProductPartialDeliveryQuery extends BaseOrderProductPartialDeliveryQuery
{
    /**
     * @return array|mixed|\Propel\Runtime\Collection\ObjectCollection
     */
    public function findNotUsedOrderProduct()
    {
        // first we get all existing ids
        $ids = array();

        /** @var OrderProductPartialDelivery $product */
        foreach ($this->find() as $product) {
            $ids[] = $product->getId();
        }

        // Then we filter order_product table to get the orders we don't have
        $query = OrderProductQuery::create();
        $query->filterById($ids, Criteria::NOT_IN);


        // And we return the return as a Collection
        return $query->find();
    }

    /**
     * @param  OrderProduct $order_product
     * @return float
     * @throws \Exception
     */
    public function getNotSentQuantity(OrderProduct $order_product)
    {
        if ($order_product->getId() === null) {
            throw new \Exception("The order product must be valid");
        }

        $sent_qty = $this->findPk($order_product->getId());

        if ($sent_qty === null) {
            // This would normaly never append, but ...
            $sent_qty = new OrderProductPartialDelivery();
            $sent_qty->setId($order_product->getId())
                ->setSentQuantity(0);
            $sent_qty->save();
        }

        return $order_product->getQuantity() - $sent_qty->getSentQuantity();
    }


    /**
     * @param  Order                            $order
     * @return OrderProductPartialDeliveryQuery
     * @throws \Exception
     */
    public function filterByNotSentOrderProducts(Order $order)
    {
        if ($order->getId() === null) {
            throw new \Exception("The order must be valid");
        }

        // find all products that aren't completely sent
        $not_completely_sent_orders = array();
        foreach ($order->getOrderProducts() as $order_product) {
            if ($this->getNotSentQuantity($order_product) > 0) {
                $not_completely_sent_orders[] = $order_product->getId();
            }
        }

        return $this->filterById($not_completely_sent_orders, Criteria::IN);
    }

    /**
     * @param  Order                                                   $order
     * @return array|mixed|\Propel\Runtime\Collection\ObjectCollection
     */
    public function findByNotSentOrderProducts(Order $order)
    {
        return $this->filterByNotSentOrderProducts($order)->find();
    }

    public function getFromOrderProductBase(OrderProduct $order_product_base)
    {
        $search = $this->findPk($order_product_base->getId());

        if ($search === null) {
            $search = new OrderProductPartialDelivery();
            $search->setId($order_product_base->getId())
                ->setSentQuantity(0);
            $search->save();
        }

        return $search;
    }
} // OrderProductPartialDeliveryQuery

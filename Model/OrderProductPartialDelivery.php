<?php

namespace PartialDelivery\Model;

use PartialDelivery\Model\Base\OrderProductPartialDelivery as BaseOrderProductPartialDelivery;
use Thelia\Model\OrderProductQuery;

class OrderProductPartialDelivery extends BaseOrderProductPartialDelivery
{
    /**
     * @return array|mixed|\Thelia\Model\OrderProduct
     */
    public function getBaseOrderProduct()
    {
        $order_product = OrderProductQuery::create()->findPk($this->getId());

        if ($order_product === null) {
            // FK constraint normally prevent this
            throw new \Exception("Error in the entry of order_product_partial_delivery with id: ".$this->getId());
        }

        return $order_product;
    }

    public function getQuantityToSend()
    {
        return $this->getBaseOrderProduct()->getQuantity()-$this->getSentQuantity();
    }

    /**
     * @param  int|float                   $i
     * @throws \Exception
     * @return OrderProductPartialDelivery $this
     */
    public function addSentQuantity($i)
    {
        if (!is_numeric($i)) {
            throw new \Exception("Argument of addSentQuantity must be an integer or a float");
        }
        $this->setSentQuantity(
            $this->getSentQuantity()+(int) $i
        );

        return $this;
    }
}

<?php

/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/


namespace PartialDelivery\Listener;


use PartialDelivery\Event\PartialDeliveryEvent;
use PartialDelivery\Event\PartialDeliveryEvents;
use PartialDelivery\Model\OrderProductPartialDeliveryQuery;
use PartialDelivery\PartialDelivery;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;

/**
 * Class Update
 * @package PartialDelivery\Listener
 * @author Thelia <info@thelia.net>
 */
class Update implements EventSubscriberInterface {

    protected $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher) {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @param PartialDeliveryEvent $event
     */
    public function update_quantity(PartialDeliveryEvent $event) {

        $order_products = $event->getOrderProducts();
        foreach($order_products as $order_product_raw) {
            /** @var \PartialDelivery\Model\OrderProductPartialDelivery $order_product */
            $order_product = $order_product_raw[0];
            $quantity = $order_product_raw[1];

            if($order_product !== null) {
                $order_product->addSentQuantity($quantity)->save();
            }

            $order = $order_product->getBaseOrderProduct()->getOrder();

            if($order === null) {
                // This has not to happend
                throw new \Exception("Error, order product ".$order_product->getId()." has no order");
            }
            $query = OrderProductPartialDeliveryQuery::create()
                ->findByNotSentOrderProducts($order);

            // If all products are sent, dispatch an event
            $order_event = new OrderEvent($order);
            if(!$query->count()) {
                $order_event->setStatus(PartialDelivery::STATUS_SENT);
            } else {
                $order_event->setStatus(PartialDelivery::STATUS_PROCESSING);
            }
            $this->getDispatcher()->dispatch(TheliaEvents::ORDER_UPDATE_STATUS, $order_event);
        }
    }

    /**
     * @param OrderEvent $event
     */
    public function set_sent(OrderEvent $event) {
        $status = $event->getStatus();
        if($status === null) {
            $status = $event->getOrder()->getStatusId();
        }

        if($status == PartialDelivery::STATUS_SENT) {
            foreach($event->getOrder()->getOrderProducts() as $order_product_base) {
                $order_product = OrderProductPartialDeliveryQuery::create()
                    ->getFromOrderProductBase($order_product_base);

                $order_product
                    ->setSentQuantity($order_product_base->getQuantity())
                    ->save();
            }
        }
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            PartialDeliveryEvents::UPDATE_SENT_QUANTITY=>array("update_quantity", 128),
            TheliaEvents::ORDER_UPDATE_STATUS=>array("set_sent", 128),
        );
    }

}
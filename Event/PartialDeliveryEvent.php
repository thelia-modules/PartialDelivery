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

namespace PartialDelivery\Event;

use PartialDelivery\Model\OrderProductPartialDelivery;
use Thelia\Core\Event\ActionEvent;

/**
 * Class PartialDeliveryEvent
 * @package PartialDelivery\Event
 * @author Thelia <info@thelia.net>
 */
class PartialDeliveryEvent extends ActionEvent
{
    /**
     * @var OrderProductPartialDelivery
     */
    protected $order_products;

    public function __construct(array $order_products)
    {
        $this->order_products = array();

        foreach ($order_products as $order_products_raw) {
            if(count($order_products_raw) == 2 &&
                $order_products_raw[0] instanceof OrderProductPartialDelivery &&
                is_numeric($order_products_raw[1])) {

                $this->order_products[] = $order_products_raw;
            }
        }
    }

    /**
     * @param  array                $order_product
     * @return PartialDeliveryEvent
     */
    public function setOrderProducts($order_products)
    {
        $this->order_products = $order_products;

        return $this;
    }

    /**
     * @return array
     */
    public function getOrderProducts()
    {
        return $this->order_products;
    }

}

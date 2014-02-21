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

namespace PartialDelivery;

use PartialDelivery\Model\OrderProductPartialDeliveryQuery;
use Propel\Runtime\Connection\ConnectionInterface;
use Thelia\Install\Database;
use Thelia\Model\Base\ModuleQuery;
use Thelia\Model\Base\OrderStatusQuery;
use Thelia\Module\BaseModule;
use PartialDelivery\Model\OrderProductPartialDelivery;


/**
 * Class PartialDelivery
 * @package PartialDelivery
 * @author Thelia <info@thelia.net>
 */
class PartialDelivery extends BaseModule
{

    const STATUS_PROCESSING = 3;

    const STATUS_SENT = 4;
    /*
     * You may now override BaseModuleInterface methods, such as:
     * install, destroy, preActivation, postActivation, preDeactivation, postDeactivation
     *
     * Have fun !
     */
    public function postActivation(ConnectionInterface $con = null)
    {
        $database = new Database($con->getWrappedConnection());

        $database->insertSql(null, array(__DIR__."/Config/thelia.sql"));


        // Init plugin with previous orders
        $query = OrderProductPartialDeliveryQuery::create()
            ->findNotUsedOrderProduct();

        // Add products that doesn't exist
        /** @var \Thelia\Model\OrderProduct $order_product */
        foreach($query as $order_product) {
            $new_product = new OrderProductPartialDelivery();
            $new_product->setId($order_product->getId())
                ->setSentQuantity(0)
                ->save();
        }
    }

    public static function getModCode() {
        return ModuleQuery::create()
            ->findOneByCode("PartialDelivery")->getId();
    }
}

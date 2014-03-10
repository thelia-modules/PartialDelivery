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

namespace PartialDelivery\Controller;

use PartialDelivery\Event\PartialDeliveryEvent;
use PartialDelivery\Form\PartialSend;
use PartialDelivery\Model\OrderProductPartialDeliveryQuery;
use PartialDelivery\Event\PartialDeliveryEvents;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Model\OrderQuery;
use Thelia\Tools\Redirect;
use Thelia\Tools\URL;

/**
 * Class SendPartialOrder
 * @package PartialDelivery\Controller
 * @author Thelia <info@thelia.net>
 */
class SendPartialOrder extends BaseAdminController
{
    public function sendPartial($order_id)
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::ORDER), array('PartialDelivery'), AccessManager::UPDATE)) {
            return $response;
        }

        $order = OrderQuery::create()
            ->findPk($order_id);

        if ($order === null) {
            throw new \Exception("This order doesn't exist");
        }

        $errmes = "";
        $products_to_update = array();

        try {
            $form = new PartialSend($this->getRequest());
            $vform = $this->validateForm($form);

            $notsent_order_products = OrderProductPartialDeliveryQuery::create()
                ->findByNotSentOrderProducts($order);

            /** @var \PartialDelivery\Model\OrderProductPartialDelivery $order_product */
            foreach ($notsent_order_products as $order_product) {
                $data = $vform->get(str_replace(".","-",$order_product->getBaseOrderProduct()->getProductSaleElementsRef()))->getData();

                if (is_numeric($data)) {
                    if((int) $data >0)
                        $products_to_update[] = array($order_product,$data);
                } else {
                    throw new \Exception("Value of product '".$order_product->getBaseOrderProduct()->getTitle().
                        "' must be an integer, '".$data."' is not a valid value.");
                }
            }
        } catch (\Exception $e) {
            $errmes = $e->getMessage();
        }

        if (count($products_to_update) > 0) {
            $event = new PartialDeliveryEvent($products_to_update);
            $this->dispatch(PartialDeliveryEvents::UPDATE_SENT_QUANTITY, $event);
        }

        Redirect::exec(
            URL::getInstance()->absoluteUrl(
                $this->getRoute(
                    "admin.order.update.view",
                    array(
                        "order_id"=>$order_id,
                        "tab"=>"modules",
                        "errmes"=>$errmes,
                    )
                )
            )
        );
    }
}

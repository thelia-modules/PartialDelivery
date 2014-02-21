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


namespace PartialDelivery\Form;

use PartialDelivery\Model\OrderProductPartialDeliveryQuery;
use Thelia\Form\BaseForm;
use Thelia\Model\OrderQuery;

class PartialSend extends BaseForm {
    /**
     *
     * in this function you add all the fields you need for your Form.
     * Form this you have to call add method on $this->formBuilder attribute :
     *
     * $this->formBuilder->add("name", "text")
     *   ->add("email", "email", array(
     *           "attr" => array(
     *               "class" => "field"
     *           ),
     *           "label" => "email",
     *           "constraints" => array(
     *               new \Symfony\Component\Validator\Constraints\NotBlank()
     *           )
     *       )
     *   )
     *   ->add('age', 'integer');
     *
     * @return null
     */
    protected function buildForm()
    {
        $request = $this->getRequest();
        $order_id = $request->get('order_id');
        $order = OrderQuery::create()->findPk($order_id);

        if($order === null) {
            throw new \Exception("You must have a valid order_id argument in your route in order to use this form.");
        }
        $query = OrderProductPartialDeliveryQuery::create()
            ->findByNotSentOrderProducts($order);

        /** @var \PartialDelivery\Model\OrderProductPartialDelivery $order_product */
        foreach($query as $order_product) {
            $products_qties = array();
            for($i=0; $i<= $order_product->getQuantityToSend(); $i++) {
                $products_qties[(string)$i]=$i;
            }

            $this->formBuilder
                ->add(
                    $order_product->getBaseOrderProduct()->getProductSaleElementsRef(),
                    "choice",
                    array(
                        'expanded'=>false,
                        'multiple'=>false,
                        'choices'=>$products_qties
                    )
                );
        }
    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return "partialsendform";
    }

}
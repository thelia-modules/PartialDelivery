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
use PartialDelivery\Model\OrderProductPartialDelivery;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Action\BaseAction;
use Thelia\Mailer\MailerFactory;
use Thelia\Core\Template\ParserInterface;
use Thelia\Model\ConfigQuery;
use Thelia\Model\MessageQuery;
/**
 * Class SendEMail
 * @package PartialDelivery\Listener
 * @author Thelia <info@thelia.net>
 */
class SendEMail extends BaseAction implements EventSubscriberInterface
{

    /**
     * @var MailerFactory
     */
    protected $mailer;
    /**
     * @var ParserInterface
     */
    protected $parser;

    function __construct(ParserInterface $parser,MailerFactory $mailer)
    {
        $this->parser = $parser;
        $this->mailer = $mailer;
    }

    /**
     * @return \Thelia\Mailer\MailerFactory
     */
    public function getMailer()
    {
        return $this->mailer;
    }

    /*
     * @params OrderEvent $order
     * Checks if order delivery module is partial delivery and send an email to the customer.
     */
    public function update_status(PartialDeliveryEvent $event) {
        //send mail
        $order_products = $event->getOrderProducts();
        if (count($order_products)) {

            $mail_code="partial_delivery_send_info";
            $locale = "en_US";
            $contact_email = ConfigQuery::read('store_email');
            $customer = null;

            if ($contact_email) {
                $message = MessageQuery::create()
                    ->filterByName($mail_code)
                    ->findOne();

                if (false === $message) {
                    throw new \Exception("Failed to load message '$mail_code'.");
                }

                /*
                 * Assign vars infos
                 */
                $this->parser->assign("nbproducts", count($order_products));
                $parser_products = array();
                $is_order_assigned=false;
                foreach($order_products as $order_product_raw) {
                    if (count($order_product_raw) === 2 &&
                        $order_product_raw[0] instanceof OrderProductPartialDelivery &&
                        is_numeric($order_product_raw[1])
                    ) {
                        /** @var OrderProductPartialDelivery $order_product */
                        $order_product = $order_product_raw[0];

                        $parser_product = &$parser_products[];
                        $parser_product["title"] = $order_product->getBaseOrderProduct()->getTitle();
                        $parser_product["quantity"] = $order_product_raw[1];

                        if(!$is_order_assigned) {
                            $order = $order_product->getBaseOrderProduct()->getOrder();
                            $locale = $order->getLang()->getLocale();
                            $customer =$order->getCustomer();

                            $this->parser->assign("customer_id", $customer->getId());
                            $this->parser->assign("order_ref", $order->getRef());
                            $this->parser->assign("order_date", $order->getCreatedAt());

                            $is_order_assigned=true;
                        }
                    }
                }
                $this->parser->assign("products", $parser_products);

                // If customer is not found
                if($customer===null) {
                    throw new \Exception("Customer not found, the mail can't be sent.");
                }

                $message
                    ->setLocale($locale);

                $instance = \Swift_Message::newInstance()
                    ->addTo($customer->getEmail(), $customer->getFirstname()." ".$customer->getLastname())
                    ->addFrom($contact_email, ConfigQuery::read('store_name'))
                ;

                // Build subject and body
                $message->buildMessage($this->parser, $instance);

                $this->getMailer()->send($instance);
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
            PartialDeliveryEvents::UPDATE_SENT_QUANTITY => array("update_status", 256)
        );
    }

} 
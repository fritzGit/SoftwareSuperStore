<?php

/*
 * Copyright 2013 pcfritz.de Onlinestore GmbH – http://www.pcfritz.de
 * all rights reserved
 */

/**
 * Paypal standard controller
 *
 * @encoding    UTF-8
 * @package     SOFTSS
 * @subpackage  SOFTSS_Paypal
 * @author      Nikolas Koumarianos <n.entwickler@pcfritz.de>
 */

require_once 'Mage/Paypal/controllers/StandardController.php';
 
class SOFTSS_Paypal_StandardController extends Mage_Paypal_StandardController
{
      
     /**
     * when paypal returns
     * The order information at this point is in POST
     * variables.  However, you don't want to "process" the order until you
     * get validation from the IPN.
     */
    public function  successAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getPaypalStandardQuoteId(true));
        
         
        $order = Mage::getModel('sales/order');
        $order->load(Mage::getSingleton('checkout/session')->getLastOrderId());
                    $order->sendNewOrderEmail();
                    $order->setEmailSent(true);
                    $order->save();     
        
        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
        $this->_redirect('checkout/onepage/success', array('_secure'=>true));
    }
}

<?php

/*
 * Copyright 2013 pcfritz.de Onlinestore GmbH – http://www.pcfritz.de
 * all rights reserved
 */

/**
 * Moneybookers processing controller
 *
 * @encoding    UTF-8
 * @package     SOFTSS
 * @subpackage  SOFTSS_Moneybookers
 * @author      Nikolas Koumarianos <n.entwickler@pcfritz.de>
 */

require_once 'Phoenix/Moneybookers/controllers/ProcessingController.php';

class SOFTSS_Moneybookers_ProcessingController extends Phoenix_Moneybookers_ProcessingController
{    
    /**
     * Show orderPlaceRedirect page which contains the Moneybookers iframe.
     */
    public function paymentAction()
    {
        try {
            $session = $this->_getCheckout();

            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($session->getLastRealOrderId());
            if (!$order->getId()) {
                Mage::throwException('No order for processing found');
            }
            $order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                Mage::helper('moneybookers')->__('The customer was redirected to Moneybookers.')
            );
            $order->save();

            $session->setMoneybookersQuoteId($session->getQuoteId());
            $session->setMoneybookersRealOrderId($session->getLastRealOrderId());
            //$session->getQuote()->setIsActive(false)->save();
            //$session->clear();
            $session->unsQuoteId();

            $this->loadLayout();
            $this->renderLayout();
        } catch (Exception $e){
            Mage::logException($e);
            parent::_redirect('checkout/cart');
        }
    }  
}

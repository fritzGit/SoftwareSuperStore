<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * Shop System Plugins - Terms of use
 *
 * This terms of use regulates warranty and liability between
 * Wirecard Central Eastern Europe (subsequently referred to as WDCEE)
 * and it's contractual partners (subsequently referred to as customer or customers)
 * which are related to the use of plugins provided by WDCEE.
 * The Plugin is provided by WDCEE free of charge for it's customers and
 * must be used for the purpose of WDCEE's payment platform integration only.
 * It explicitly is not part of the general contract between WDCEE and it's customer.
 * The plugin has successfully been tested under specific circumstances
 * which are defined as the shopsystem's standard configuration (vendor's delivery state).
 * The Customer is responsible for testing the plugin's functionality
 * before putting it into production enviroment.
 * The customer uses the plugin at own risk. WDCEE does not guarantee it's full
 * functionality neither does WDCEE assume liability for any disadvantage related
 * to the use of this plugin. By installing the plugin into the shopsystem the customer
 * agrees to the terms of use. Please do not use this plugin if you do not agree to the terms of use!
 *
 * @category   Phoenix
 * @package    Phoenix_WirecardQPay
 * @copyright  Copyright (c) 2008 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 */

class Phoenix_WirecardQPay_ProcessingController extends Mage_Core_Controller_Front_Action
{
    public function _expireAjax()
    {
        if (!$this->getCheckout()->getQuote()->hasItems())
        {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

  /**
     * Get singleton of Checkout Session Model
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

  /**
     * checkout Page for IFrame include
     */
    public function qpayCheckoutAction()
    {
        $session = $this->getCheckout();

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getLastRealOrderId());
        if(!$this->_succeeded($order))
        {
            $order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true, Mage::helper('qpay')->__('Customer was redirected to QPay.'))->save();
            // Save quote id in session and clean it
            $session->setQPayQuoteId($session->getQuoteId());
            $session->getQuote()->setIsActive(false)->save();
            $session->clear();

            $paymentInst = $order->getPayment()->getMethodInstance();
            if($paymentInst->getConfigData('useIFrame') && !$session->getIsQMoreIframe())
            {
                $this->loadLayout();
                $this->renderLayout();
            }
            else
            {
                $session->unsIsQMoreIframe();
                try
                {
                    $this->_redirectUrl($paymentInst->getUrl());
                }
                catch(Exception $e)
                {
                    if($paymentInst->getConfigData('useSeamless'))
                    {
                        throw $e;
                    }
                    else
                    {
                        if ($quoteId = $this->getCheckout()->getLastQuoteId()) {
                            $quote = Mage::getModel('sales/quote')->load($quoteId);
                            if ($quote->getId()) {
                                $quote->setIsActive(true)->save();
                                $this->getCheckout()->setQuoteId($quoteId);
                            }
                        }
                        $this->getCheckout()->addNotice($e->getMessage());
                        $this->_redirectUrl('/checkout/cart/');
                    }
                }
                return;
            }
        }
        else
        {
            $this->norouteAction();
        }
    }

    public function isIframeAction()
    {
        $result = Array();
        $paymentMethod = $this->getRequest()->getParam('paymentMethod', null);
        if(!$paymentMethod)
        {
            $result['isIframe'] = false;
        }
        else if(substr($paymentMethod, 0,5) == 'qenta')
        {
            $session = $this->getCheckout();
    
            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($session->getLastRealOrderId());
            $paymentInst = $order->getPayment()->getMethodInstance();
            if($paymentInst->getConfigData('useIFrame') && $paymentInst->getConfigData('useSeamless'))
            {
                $session = $this->getCheckout();
                $session->setIsQMoreIframe(true);
                $result['isIframe'] = true;
            }
            else
            {
                $result['isIframe'] = false;
            }
        }
        else 
        {
            $result['isIframe'] = false;
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * QPay returns POST variables to this action
     */
    public function checkresponseAction()
    {
        try {
            $session = $this->getCheckout();
            $order = Mage::getModel('sales/order');
            $order->load($session->getLastOrderId());

                // load order ID
            if(!$order->getId())
                throw new Exception('Order ID not found.');

            // the customer has canceled the payment. show cancel message.
            if ($order->isCanceled())
            {
                if ($quoteId = $session->getLastQuoteId()) {
                    $quote = Mage::getModel('sales/quote')->load($quoteId);
                    if ($quote->getId()) {
                        $quote->setIsActive(true)->save();
                        $session->setQuoteId($quoteId);
                    }
                }
                if(!$consumerMessage = $order->getPayment()->getAdditionalInformation('consumerMessage'))
                {
                    //fallback message if no consumerMessage has been set
                    $consumerMessage = 'Order has been canceled.';
                }
                throw new Exception(Mage::helper('qpay')->__($consumerMessage));
            }

                // get sure order status has changed since redirect
            if ($order->getStatus() == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT)
                throw new Exception(Mage::helper('qpay')->__('Sorry, your payment has not confirmed by the payment provider.'));

                // payment is okay. show success page.
            //$this->_redirect('checkout/onepage/success');
            $this->getCheckout()->setLastSuccessQuoteId($session->getLastQuoteId());
            $this->getCheckout()->setResponseRedirectUrl('checkout/onepage/success');
        }
        catch (Exception $e)
        {
            $this->getCheckout()->addNotice($e->getMessage());
            $this->getCheckout()->setResponseRedirectUrl('checkout/cart/');
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Process transaction confirm message
     */
    public function confirmAction()
    {
        try
        {
            /** verify call */
            $data = $this->_checkReturnedPost();
            // process action
            $this->_processConfirmState($data['paymentState'], $data);
            $this->order->save();
            // send confirmation for status change
            die('<QPAY-CONFIRMATION-RESPONSE result="OK" />');
        }
        catch (Exception $e)
        {
            $orderId = (!empty($data['orderId'])) ? $data['orderId'] : '';
            Mage::log('QPay transaction status update failed: '.$e->getMessage(). '('.$orderId.')');
            Mage::log($e->getMessage() . "\n" . $e->getTraceAsString(), null, 'qpay_exception.log');
            die('<QPAY-CONFIRMATION-RESPONSE result="NOK" message="' . $e->getMessage() . '" />');
        }
    }
    /**
     * check if order already has been successfully processed.
     * @param $order - Magento Order Object
     * @return bool
     */
    protected function _succeeded($order)
    {
        $history = $order->getAllStatusHistory();
        if($paymentInst =  $order->getPayment()->getMethodInstance())
        {
            foreach($history AS $entry)
            {
                if(strtolower($entry->getStatusLabel()) == strtolower(Mage_Sales_Model_Order::STATE_PROCESSING))
                {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Checking POST variables.
     * Creating invoice if payment was successfull or cancel order if payment was declined
     *
     * @param bool $seamless If post is from new Api we need the raw POST data
     */
    protected function _checkReturnedPost($seamless = false)
    {
        if (!$this->getRequest()->isPost())
            throw new Exception('Not a POST message.');

        $data = $this->getRequest()->getPost();

        if (empty($data) || empty($data['orderId']))
            throw new Exception('POST data is empty.');

            // load order
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($data['orderId']);
        if (!$order->getId())
            throw new Exception('Order ID not found.');
        $this->order = $order;

        $paymentInst = $order->getPayment()->getMethodInstance();
        $paymentInst->setResponse($data);
        $this->paymentInst = $paymentInst;

        if ($data['paymentState'] == 'SUCCESS' && !$seamless)
        {
            if (!$paymentInst->parseResponse())
                throw new Exception('Security key does not match.');
        }

        $this->paymentInst = $paymentInst;

        if ($seamless) {
            return array('post' => $data, 'raw' => $this->getRequest()->getRawBody());
        } else {
            return $data;
        }
        return $data;
    }

    /**
     * Confirm Controller for Seamless Payments
     */
    public function seamlessConfirmAction()
    {
        try {
            $data            = $this->_checkReturnedPost(true);
            $storeId         = $this->order->getStoreId();
            $methodCode      = $this->paymentInst->getCode();
            $secretKey       = Mage::getStoreConfig('payment/'.$methodCode.'/secret_key', $storeId);
            $confirmResponse = WirecardCEE_Client_QPay_Return::generateConfirmResponseString();
            $return          = WirecardCEE_Client_QPay_Return::createReturnInstance($data['raw'], $secretKey);

            if($return->validate()) {
                $this->_confirmState($data['post'], $return);

            } else {
                throw new Exception('Unhandled QPay action "'.$data['paymentState'].'".');
            }

            $this->order->save();
            // send confirmation for status change
            die($confirmResponse);

        } catch (Exception $e) {
            $orderId = (!empty($data['orderId'])) ? $data['orderId'] : '';
            Mage::log('QPay transaction status update failed: '.$e->getMessage(). '('.$orderId.')');
            Mage::log($e->getMessage() . "\n" . $e->getTraceAsString(), null, 'qpay_exception.log');
            $confirmResponse = WirecardCEE_Client_QPay_Return::generateConfirmResponseString($e->getMessage());
            die($confirmResponse);
        }
    }

    /**
     * Check the state response of the payment and perform corresponding order action
     *
     * @param array $data The POST data from the payment response
     * @param object $return An WirecardCEE_Client_QPay_Return_* object
     * @throws Exception
     */
    protected function _confirmState($data, $return)
    {
        switch ($return->getPaymentState()) {
            case 'SUCCESS':
                $this->_confirmOrder($data);
                break;

            case 'CANCEL':
                $this->_cancelOrder();
                break;

            case 'FAILURE':
                $msg = array();
                $consumerMessage = array();
                foreach ($return->getErrors() as $error) {
                    $msg[] = 'QPay Error: ' . $error->getMessage();
                    $consumerMessage[] = $error->getConsumerMessage();
                }

                if (!$this->_succeeded($this->order)) {
                    $this->order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true, Mage::helper('qpay')->__('An error occured during the payment process:').' '.implode('|', $msg))->save();
                    $this->order->cancel();
                    $payment = $this->order->getPayment();
                    $payment->setAdditionalInformation('consumerMessage', implode(' ' ,$consumerMessage));
                }
                break;

            default:
                throw new Exception('Unknown paymentState: '.$data['paymentState']);
        }
    }

    /**
     * Cancel an order
     */
    protected function _cancelOrder()
    {
        if (!$this->_succeeded($this->order)) {
            if ($this->order->canUnhold()) {
                $this->order->unhold();
            }
            $this->order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, Mage::helper('qpay')->__('Customer canceled the payment process'))->save();
            $this->order->cancel();
        }
    }

    /**
     * Confirm the payment of an order
     *
     * @param array $data
     */
    protected function _confirmOrder($data)
    {
        if (!$this->_succeeded($this->order)) {
            $this->order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, Mage::helper('qpay')->__('The amount has been authorized and captured by QPay.'))->save();
        }
        // send new order email to customer
        $this->order->sendNewOrderEmail();
        // invoice payment
        if ($this->order->canInvoice()) {

            $invoice = $this->order->prepareInvoice();
            $invoice->register()->capture();
            Mage::getModel('core/resource_transaction')
                  ->addObject($invoice)
                  ->addObject($invoice->getOrder())
                  ->save();
        }
        $payment                     = $this->order->getPayment();
        $additionalInformation       = Array();
        $additionalInformationString = '';

        foreach($data as $fieldName => $fieldValue) {
            switch ($fieldName) {
                case 'amount':
                case 'currency':
                case 'language':
                case 'responseFingerprint':
                case 'responseFingerprintOrder':
                case 'form_key':
                case 'paymentState':
                case 'orderId':
                    break;

                default:
                    $additionalInformation[htmlentities($fieldName)] = htmlentities($fieldValue);
                    $additionalInformationString                    .= ' | '.$fieldName.' - '.$fieldValue;
            }
        }

        if (count($additionalInformation) != 0) {

            $payment->setAdditionalInformation($additionalInformation);
            $payment->setAdditionalData(serialize($additionalInformation));

            if ($payment->hasAdditionalInformation()) {
                Mage::log('Added Additional Information to Order ' . $data['orderId'] . ' :'. $additionalInformationString);
            }
        }
        $payment->save();
    }

    /**
     * Store anonymized Payment Data from Seamless Checkout in the Session
     */
    public function saveSessInfoAction()
    {
        $payment = $this->getRequest()->getPost();

        if (!empty($payment) && isset($payment['payment']) && !empty($payment['payment'])) {
            $payment = $payment['payment'];

            if ($payment['method']=='qenta_cc' && !empty($payment['cc_owner']) && !empty($payment['cc_type'])
                && !empty($payment['cc_number']) && !empty($payment['cc_exp_month']) && !empty($payment['cc_exp_year'])
                && !empty($payment['additional_data'])) {
                Mage::getSingleton('core/session')->setQpayPaymentInfo($payment);
            }
        }

        return;
    }

    /**
     * Delete the anonymized Qpay Session Data stored from Seamless Checkout
     */
    public function deleteSessInfoAction()
    {
        if ($this->getRequest()->isPost()) {
            Mage::getSingleton('core/session')->unsetData('qpay_payment_info');
        }
        return;
    }

    /**
     * The controller action used for older browsers to return datastorage parameters in an iFrame.
     */
    public function storereturnAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    protected function _processConfirmState($state, $data)
    {
        switch ($state) {
            case 'SUCCESS':
                $this->_confirmOrder($data);
                break;

            case 'CANCEL':
                $this->_cancelOrder();
                break;

            case 'FAILURE':
                $msg = (!empty($data['message'])) ? $data['message'] : '';
                if (!$this->_succeeded($this->order)) {
                    $this->order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT, true, Mage::helper('qpay')->__('An error occured during the payment process:').' '.$msg)->save();
                    $this->order->cancel();
                    $payment = $this->order->getPayment();
                    $payment->setAdditionalInformation('consumerMessage', $data['consumerMessage']);
                }
                break;

            default:
                throw new Exception('Unhandled QPay action "'.$data['paymentState'].'".');
        }
    }
}

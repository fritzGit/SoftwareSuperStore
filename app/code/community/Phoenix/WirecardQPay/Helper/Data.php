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

class Phoenix_WirecardQPay_Helper_Data extends Mage_Payment_Helper_Data
{
    /**
     * Check if the Semaless Data Storage is enabled
     *
     * @return boolean
     */
    public function isDataStorageEnabled()
    {
        if (Mage::getSingleton('checkout/session')->getData('qpay_data_storage_enabled')) {
            return true;
        }
        return false;
    }

    /**
     * Process the Server to Server communication to Wirecard CEE to initialize the Data Storage
     *
     * @param string $customerId
     * @param string $storeId
     * @param string $shopId
     *
     * @return mixed
     */
    public function startWirecardCEE($customerId, $storeId, $shopId, $methodCode)
    {
        $secretKey  = Mage::getStoreConfig('payment/'.$methodCode.'/secret_key', $storeId);
        $returnUrl  = Mage::getUrl('qpay/processing/storereturn', array('_secure' => true));
        $quoteId    = Mage::getSingleton('checkout/session')->getQuote()->getId();
        $language   = substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2);

        $dataStorageInit = new WirecardCEE_Client_DataStorage_Request_Initiation($customerId, $shopId, $language, $returnUrl, $secretKey);
        $storageId       = '';
        try {

            $response = $dataStorageInit->initiate($quoteId);

            if ($response->getStatus() == WirecardCEE_Client_DataStorage_Response_Initiation::STATE_SUCCESS) {

                $storageId       = $response->getStorageId();
                $javascriptUrl   = $response->getJavascriptUrl();
                Mage::getSingleton('checkout/session')->setData('qpay_data_storage_enabled', '1');

                return array('storagId' => $storageId, 'javascriptUrl' => $javascriptUrl);

            } else {

                Mage::getSingleton('checkout/session')->setData('qpay_data_storage_enabled', false);
                $dsErrors = $response->getErrors();

                foreach ($dsErrors as $error) {
                    Mage::log($error->getMessage(), true, 'qpay_exception.log');
                }
                return false;
            }
        } catch(WirecardCEE_Exception $e) {

            //communication with dataStorage failed. we choose a none dataStorage fallback
            Mage::getSingleton('checkout/session')->setData('qpay_data_storage_enabled', false);
            Mage::logException($e);
            return false;
        }
    }
}
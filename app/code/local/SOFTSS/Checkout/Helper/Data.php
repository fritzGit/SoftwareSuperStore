<?php

/*
 * Copyright 2013 pcfritz.de Onlinestore GmbH – http://www.pcfritz.de
 * all rights reserved
 */

/**
 * Checkout Helper
 *
 * @encoding    UTF-8
 * @package     SOFTSS
 * @subpackage  SOFTSS_Checkout
 * @author      Nikolas Koumarianos <n.entwickler@pcfritz.de>
 */
class SOFTSS_Checkout_Helper_Data extends Mage_Checkout_Helper_Data
{
    
    const XML_SOFTDISTIBUTION_RESELLERID = 'checkout/softdistribution/resellerid';
    const XML_SOFTDISTIBUTION_PASSWORD = 'checkout/softdistribution/password';
    const XML_SOFTDISTIBUTION_URL = 'checkout/softdistribution/url';
    
    public function getSoftDistributionUrl() 
    {
        $items= Mage::getSingleton('checkout/session')->getQuote()->getAllItems();      
        
        $aData = array();
        
        foreach($items as $item) {
            
           $aData[] = $item->getProduct()->getSoftssSupplierProductId();
           $aData[] = $item->getQty();    
    
        }
                                                         
        $products =  implode(";", $aData);
        
        $url = Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_URL);
        $url .= '?resellerid='.Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_RESELLERID);
        $url .= '&pass='.md5(Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_PASSWORD));
        $url .= '&products='.$products;
        
        
        return $url;
        
    }

}


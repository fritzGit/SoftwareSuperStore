<?php

/*
 * Copyright 2013 pcfritz.de Onlinestore GmbH – http://www.pcfritz.de
 * all rights reserved
 */

/**
 * Downloadable Helper
 *
 * @encoding    UTF-8
 * @package     SOFTSS
 * @subpackage  SOFTSS_Downloadable
 * @author      Nikolas Koumarianos <n.entwickler@pcfritz.de>
 * 
 */

class SOFTSS_Downloadable_Helper_Data extends Mage_Downloadable_Helper_Data
{
   
    public function getSoftdistributionInfo($item)
    {
        $data = array();
       
        $softItem = Mage::getModel('softd/softd')->getCollection()
                ->addFieldToFilter('itemid', $item->getOrderItemId())
                ->addFieldToFilter('orderref', $item->getPurchased()->getOrderId())
                ->getFirstItem();
        
        $data['url'] = $softItem->getDownloadlink();
        $data['serialnumber'] = explode(',', $softItem->getSerialnumber());
                
        return $data;
    }    
 
}

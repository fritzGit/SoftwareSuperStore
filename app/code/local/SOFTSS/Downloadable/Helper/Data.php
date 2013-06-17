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
   
    public function getDownloadSoftdistributionUrl($item)
    {
       
        $softItem = Mage::getModel('softdistribution/softdistribution')->getCollection()
                ->addFieldToFilter('', $item->getId())
                ->addFieldToFilter('orderref', $item->getPurchased()->getOrderId())
                ->getFirstItem();
        
        if($url = $softItem->getDownloadlink()) {
            return $url;
        }
        
        return;
    }
}

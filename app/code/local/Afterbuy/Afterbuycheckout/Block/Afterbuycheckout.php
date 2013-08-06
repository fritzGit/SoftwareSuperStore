<?php
class Afterbuy_Afterbuycheckout_Block_Afterbuycheckout extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getAfterbuycheckout()     
     { 
        if (!$this->hasData('afterbuycheckout')) {
            $this->setData('afterbuycheckout', Mage::registry('afterbuycheckout'));
        }
        return $this->getData('afterbuycheckout');
        
    }
}
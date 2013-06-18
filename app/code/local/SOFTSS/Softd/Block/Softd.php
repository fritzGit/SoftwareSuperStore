<?php
class SOFTSS_Softd_Block_Softd extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getSoftd()     
     { 
        if (!$this->hasData('softd')) {
            $this->setData('softd', Mage::registry('softd'));
        }
        return $this->getData('softd');
        
    }
}
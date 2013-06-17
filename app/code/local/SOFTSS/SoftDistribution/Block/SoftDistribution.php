<?php
class SOFTSS_Softdistribution_Block_Softdistribution extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getSoftdistribution()     
     { 
        if (!$this->hasData('softdistribution')) {
            $this->setData('softdistribution', Mage::registry('softdistribution'));
        }
        return $this->getData('softdistribution');
        
    }
}
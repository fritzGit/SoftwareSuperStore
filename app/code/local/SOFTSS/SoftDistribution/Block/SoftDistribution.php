?php
class SOFTSS_SoftDistribution_Block_SoftDistribution extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getSoftDistribution()     
     { 
        if (!$this->hasData('softdistribution')) {
            $this->setData('softdistribution', Mage::registry('softdistribution'));
        }
        return $this->getData('softdistribution');
        
    }
}
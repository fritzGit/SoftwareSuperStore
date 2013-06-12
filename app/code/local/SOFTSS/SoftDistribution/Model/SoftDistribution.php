<?php

class SOFTSS_SoftDistribution_Model_SoftDistribution extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('softdistribution/softdistribution');
    }
}
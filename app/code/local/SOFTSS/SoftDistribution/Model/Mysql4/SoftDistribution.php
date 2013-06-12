<?php

class SOFTSS_SoftDistribution_Model_Mysql4_SoftDistribution extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the softdistribution_id refers to the key field in your database table.
        $this->_init('softdistribution/softdistribution', 'softdistribution_id');
    }
}
<?php

class SOFTSS_Softd_Model_Mysql4_Softd extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the softd_id refers to the key field in your database table.
        $this->_init('softd/softd', 'id');
    }
}
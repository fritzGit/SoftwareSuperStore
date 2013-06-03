<?php

class Afterbuy_Afterbuycheckout_Model_Mysql4_Afterbuycheckout extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the afterbuycheckout_id refers to the key field in your database table.
        $this->_init('afterbuycheckout/afterbuycheckout', 'afterbuycheckout_id');
    }
}
<?php

class Afterbuy_Afterbuycheckout_Model_Mysql4_Afterbuycheckout_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('afterbuycheckout/afterbuycheckout');
    }
}
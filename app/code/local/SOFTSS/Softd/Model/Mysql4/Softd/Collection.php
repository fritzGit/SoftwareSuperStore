<?php

class SOFTSS_Softd_Model_Mysql4_Softd_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('softd/softd');
    }
}
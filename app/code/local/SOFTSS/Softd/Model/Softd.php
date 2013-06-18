<?php

class SOFTSS_Softd_Model_Softd extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('softd/softd');
    }
}
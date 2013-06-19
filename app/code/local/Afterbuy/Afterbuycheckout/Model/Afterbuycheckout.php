<?php

class Afterbuy_Afterbuycheckout_Model_Afterbuycheckout extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('afterbuycheckout/afterbuycheckout');
    }

	public function read($order_id)
	{
		$sql = "SELECT * FROM `afterbuyorderdata` WHERE
				`shoporderid` = '".$order_id."'";

		// fetch read database connection that is used in Mage_Core module
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');

        // now $write is an instance of Zend_Db_Adapter_Abstract
        //$read_result = $read->query($sql);

		$read_result = $read->fetchAll($sql);
		return $read_result;
	}

        public function getAfterbuyId($order_id)
	{
		$sql = "SELECT `aid` FROM `afterbuyorderdata` WHERE
				`shoporderid` = '".$order_id."'";

		// fetch read database connection that is used in Mage_Core module
                $read = Mage::getSingleton('core/resource')->getConnection('core_read');

		$read_result = $read->fetchOne($sql);
		return $read_result;
	}

}
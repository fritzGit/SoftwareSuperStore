<?php
/**
 * Created by
 * User: jgalvez
 * @author juan galvez
 * @category SOFTSS
 * @package SOFTSS_Topseller
 *
 */
class SOFTSS_Topseller_Model_Resource_Homepagetopseller_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('softsstopseller/homepagetopseller');
    }

}

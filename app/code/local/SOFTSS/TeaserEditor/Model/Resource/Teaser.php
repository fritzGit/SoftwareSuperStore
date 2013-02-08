<?php
/**
 * Created by
 * User: jgalvez
 * @author juan galvez
 * @category SOFTSS
 * @package SOFTSS_TeaserEditor
 *
 */
class SOFTSS_TeaserEditor_Model_Resource_Teaser extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Primary key auto increment flag
     *
     * @var bool
     */
    protected $_isPkAutoIncrement = true;

    /**
     * Define main table name
     */
    protected function _construct()
    {
        $this->_init('softssteasereditor/teaser','id');
    }
}

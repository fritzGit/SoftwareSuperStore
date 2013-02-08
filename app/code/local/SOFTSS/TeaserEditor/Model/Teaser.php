<?php
/**
 * Created by
 * User: jgalvez
 * @author juan galvez
 * @category SOFTSS
 * @package SOFTSS_TeaserEditor
 *
 */
class SOFTSS_TeaserEditor_Model_Teaser extends Mage_Core_Model_Abstract
{
    protected $_oCategoryTeaserCollection;

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('softssteasereditor/teaser');
    }

    /*
     * @return collection of category teaser images
     */
    public function getCategoryTeaserCollection()
    {
        if(is_null($this->_oCategoryTeaserCollection))
        {
            // filter only category teasers
            $this->_oCategoryTeaserCollection = Mage::getModel('softssteasereditor/teaser')->getCollection()->addTeaserGroupFilter(4);
        }

        return $this->_oCategoryTeaserCollection;
    }

}
?>
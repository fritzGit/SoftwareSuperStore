<?php
/**
 * Created by
 * User: jgalvez
 * @author juan galvez
 * @category SOFTSS
 * @package SOFTSS_TeaserEditor
 *
 */
class SOFTSS_TeaserEditor_Model_Homepage extends Mage_Core_Model_Abstract
{
    protected $_oSliderTeaserCollection;
    protected $_oLeftTeaserCollection;
    protected $_oRightTeaserCollection;
    protected $_oBottomTeaserCollection;

    /*
     * @return collection of homepage slider images
     */
    public function getSliderTeaserCollection()
    {
        if(is_null($this->_oSliderTeaserCollection))
        {
            // filter only main slider teasers
            $this->_oSliderTeaserCollection = Mage::getModel('softssteasereditor/teaser')->getCollection()->addTeaserGroupFilter(4);
        }

        return $this->_oSliderTeaserCollection;
    }

    /*
     * @return collection of homepage right bar teaser images
     */
    public function getRightTeaserCollection()
    {
        if(is_null($this->_oRightTeaserCollection))
        {
            // filter only homepage right bar teasers
            $this->_oRightTeaserCollection  = Mage::getModel('softssteasereditor/teaser')->getCollection()->addTeaserGroupFilter(3);
        }

        return $this->_oRightTeaserCollection;
    }

    /*
     * @return collection of homepage left bar teaser images
     */
    public function getLeftTeaserCollection()
    {
        if(is_null($this->_oLeftTeaserCollection))
        {
            // filter only homepage left bar teasers
            $this->_oLeftTeaserCollection = Mage::getModel('softssteasereditor/teaser')->getCollection()->addTeaserGroupFilter(1);
        }

        return $this->_oLeftTeaserCollection;
    }

    /*
     * @return collection of homepage bottom bar teaser images
     */
    public function getBottomTeaserCollection()
    {
        if(is_null($this->_oBottomTeaserCollection))
        {
            // filter only homepage bottom teasers
            $this->_oBottomTeaserCollection = Mage::getModel('softssteasereditor/teaser')->getCollection()->addTeaserGroupFilter(2);
        }

        return $this->_oBottomTeaserCollection;
    }
}

?>

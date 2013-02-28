<?php
class Softss_TeaserEditor_Block_Teaser extends Mage_Core_Block_Template
{

    /**
     * Main Slider Teasers
     */
    protected $_aMainSliderTeasers;

    /**
     * Retrieve list of homepage main slider teasers
     *
     * @return $_aMainSliderTeasers
     */
    public function getHomepageSliderTeasers()
    {
        if (is_null($this->_aMainSliderTeasers)) {
            $this->_aMainSliderTeasers = Mage::getModel('softssteasereditor/homepage')->getSliderTeaserCollection();
        }

        return $this->_aMainSliderTeasers;
    }

     /**
     * Check if current url is url for home page
     *
     * @return true
     */
    public function getIsHomePage()
    {
        return $this->getUrl('') == $this->getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true));
    }
}
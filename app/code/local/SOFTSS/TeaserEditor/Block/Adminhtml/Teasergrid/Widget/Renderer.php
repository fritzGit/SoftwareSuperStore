<?php
/**
 * User: jgalvez
 */
class SOFTSS_TeaserEditor_Block_Adminhtml_Teasergrid_Widget_Renderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row) {
        return ($this->_getImage($row));
    }

    protected function _getImage(Varien_Object $row) {
        $img = $row->image != '' ? '<img width="50" height="50" src="'.Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).Mage::helper('softssteasereditor')->getTeaserImageDir().$row->image.'" title="'.$row->image.'" alt="'.$row->image.'" />' : '';
        return $img;
    }
}
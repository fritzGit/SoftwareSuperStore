<?php
/**
 * Created by
 * User: jgalvez
 * @author juan galvez
 * @pagage SOFTSS
 * @subpackage SOFTSS_TeaserEditor
 */
class SOFTSS_TeaserEditor_Block_Adminhtml_Teasergrid extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected $_addButtonLabel = 'Add New Teaser';

    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_teasergrid';
        $this->_blockGroup = 'softssteasereditor';
        $this->_headerText = Mage::helper('softssteasereditor')->__('Teasers');
    }

    protected function _prepareLayout()
    {
        $this->setChild( 'grid', $this->getLayout()->createBlock($this->_blockGroup.'/' . $this->_controller . '_grid', $this->_controller . '.grid')->setSaveParametersInSession(true) );
        return parent::_prepareLayout();
    }
}
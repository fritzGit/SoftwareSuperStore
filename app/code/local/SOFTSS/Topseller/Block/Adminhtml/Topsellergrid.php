<?php
/**
 * Created by jgalvez
 */
class SOFTSS_Topseller_Block_Adminhtml_Topsellergrid extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected $_addButtonLabel = 'Add New Homepage Topseller';

    public function __construct()
    {
        parent::__construct();
        $this->_controller = 'adminhtml_topsellergrid';
        $this->_blockGroup = 'softsstopseller';
        $this->_headerText = Mage::helper('softsstopseller')->__('Homepage Topsellers');
    }

    protected function _prepareLayout()
    {
        $this->setChild( 'grid', $this->getLayout()->createBlock('softsstopseller/adminhtml_topsellergrid_grid', 'adminhtml_topsellergrid.grid')->setSaveParametersInSession(true) );
        return parent::_prepareLayout();
    }
}

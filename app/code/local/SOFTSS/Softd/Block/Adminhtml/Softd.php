<?php
class SOFTSS_Softd_Block_Adminhtml_Softd extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_softd';
    $this->_blockGroup = 'softd';
    $this->_headerText = Mage::helper('softd')->__('Product Info');
    //$this->_addButtonLabel = Mage::helper('softd')->__('Add Item');
    parent::__construct();
    $this->removeButton('add');

    
  }
}
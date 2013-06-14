<?php
class SOFTSS_SoftDistribution_Block_Adminhtml_SoftDistribution extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_softdistribution';
    $this->_blockGroup = 'softdistribution';
    $this->_headerText = Mage::helper('softdistribution')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('softdistribution')->__('Add Item');
    parent::__construct();
  }
}
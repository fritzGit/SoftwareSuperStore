<?php
class Afterbuy_Afterbuycheckout_Block_Adminhtml_Afterbuycheckout extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_afterbuycheckout';
    $this->_blockGroup = 'afterbuycheckout';
    $this->_headerText = Mage::helper('afterbuycheckout')->__('Afterbuy Config Manager');
    $this->_addButtonLabel = Mage::helper('afterbuycheckout')->__('Insert Afterbuy Config');
    parent::__construct();
  }
}
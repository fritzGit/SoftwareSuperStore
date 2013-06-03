<?php

class Afterbuy_Afterbuycheckout_Block_Adminhtml_Afterbuycheckout_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('afterbuycheckout_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('afterbuycheckout')->__('Afterbuy Account Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('afterbuycheckout')->__('Afterbuy Account Information'),
          'title'     => Mage::helper('afterbuycheckout')->__('Afterbuy Account Information'),
          'content'   => $this->getLayout()->createBlock('afterbuycheckout/adminhtml_afterbuycheckout_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}
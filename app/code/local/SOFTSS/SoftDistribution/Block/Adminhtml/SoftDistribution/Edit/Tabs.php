<?php

class SOFTSS_Softdistribution_Block_Adminhtml_Softdistribution_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('softdistribution_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('softdistribution')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('softdistribution')->__('Item Information'),
          'title'     => Mage::helper('softdistribution')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('softdistribution/adminhtml_softdistribution_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}
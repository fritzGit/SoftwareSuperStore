<?php

class SOFTSS_Softd_Block_Adminhtml_Softd_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('softd_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('softd')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('softd')->__('Item Information'),
          'title'     => Mage::helper('softd')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('softd/adminhtml_softd_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}
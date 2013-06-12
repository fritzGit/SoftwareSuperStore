<?php

class SOFTSS_SoftDistribution_Block_Adminhtml_SoftDistribution_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('softdistribution_form', array('legend'=>Mage::helper('softdistribution')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('softdistribution')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('softdistribution')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('softdistribution')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('softdistribution')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('softdistribution')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('softdistribution')->__('Content'),
          'title'     => Mage::helper('softdistribution')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getSoftDistributionData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getSoftDistributionData());
          Mage::getSingleton('adminhtml/session')->setSoftDistributionData(null);
      } elseif ( Mage::registry('softdistribution_data') ) {
          $form->setValues(Mage::registry('softdistribution_data')->getData());
      }
      return parent::_prepareForm();
  }
}
<?php

class Afterbuy_Afterbuycheckout_Block_Adminhtml_Afterbuycheckout_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('afterbuycheckout_form', array('legend'=>Mage::helper('afterbuycheckout')->__('Afterbuy Settings')));
     
      $fieldset->addField('user_name', 'text', array(
          'label'     => Mage::helper('afterbuycheckout')->__('User Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'user_name',
      ));

     $fieldset->addField('partner_id', 'text', array(
          'label'     => Mage::helper('afterbuycheckout')->__('Partner ID'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'partner_id',
      ));

	  $fieldset->addField('shopbetreiber_mailadresse', 'text', array(
          'label'     => Mage::helper('afterbuycheckout')->__('Mailadresse an die Fehlermeldungen geschickt werden'),
          'class'     => 'required-entry',
          'required'  => false,
          'name'      => 'shopbetreiber_mailadresse',
      ));
	  
	   $fieldset->addField('partner_pass', 'text', array(
          'label'     => Mage::helper('afterbuycheckout')->__('Partner Password'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'partner_pass',
      ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('afterbuycheckout')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('afterbuycheckout')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('afterbuycheckout')->__('Disabled'),
              ),
          ),
      ));
      $fieldset->addField('feedback', 'select', array(
          'label'     => Mage::helper('afterbuycheckout')->__('Feedback'),
          'name'      => 'feedback',
          'values'    => array(
              array(
                  'value'     => 0,
                  'label'     => Mage::helper('afterbuycheckout')->__('Feedbackdatum setzen und KEINE automatische Erstkontaktmail versenden'),
              ),

              array(
                  'value'     => 1,
                  'label'     => Mage::helper('afterbuycheckout')->__('Feedbackdatum NICHT setzen, aber automatische Erstkontaktmail versenden'),
              ),
              
              array(
                  'value'     => 2,
                  'label'     => Mage::helper('afterbuycheckout')->__('Feedbackdatum setzen und automatische Erstkontaktmail versenden'),
              ),
          ),
      ));

      $fieldset->addField('artikelerkennung', 'select', array(
          'label'     => Mage::helper('afterbuycheckout')->__('Artikelerkennung'),
          'name'      => 'artikelerkennung',
          'values'    => array(
              array(
                  'value'     => 0,
                  'label'     => Mage::helper('afterbuycheckout')->__('Product ID'),
              ),

              array(
                  'value'     => 1,
                  'label'     => Mage::helper('afterbuycheckout')->__('Artikelnummer'),
              ),
              array(
                  'value'     => 2,
                  'label'     => Mage::helper('afterbuycheckout')->__('EAN'),
              ),
          ),
      ));

      $fieldset->addField('kundenerkennung', 'select', array(
          'label'     => Mage::helper('afterbuycheckout')->__('Kundenerkennung'),
          'name'      => 'kundenerkennung',
          'values'    => array(
              array(
                  'value'     => 0,
                  'label'     => Mage::helper('afterbuycheckout')->__('Standard EbayName'),
              ),

              array(
                  'value'     => 1,
                  'label'     => Mage::helper('afterbuycheckout')->__('Email'),
              ),
              array(
                  'value'     => 2,
                  'label'     => Mage::helper('afterbuycheckout')->__('EKNummer (wenn im Shop vorhanden)'),
              ),
          ),
      ));

      $fieldset->addField('versand', 'select', array(
          'label'     => Mage::helper('afterbuycheckout')->__('Versandberechnung'),
          'name'      => 'versand',
          'values'    => array(
              array(
                  'value'     => 0,
                  'label'     => Mage::helper('afterbuycheckout')->__('Versandermittlung durch Afterbuy (nur bei Bestandsartikelerkennung)'),
              ),

              array(
                  'value'     => 1,
                  'label'     => Mage::helper('afterbuycheckout')->__('Versandberechnung durch Magento'),
              ),
              
          ),
      ));
	  
      $fieldset->addField('check_doppelbestellung', 'select', array(
          'label'     => Mage::helper('afterbuycheckout')->__('Doppelbestellungen verhindern'),
          'name'      => 'check_doppelbestellung',
          'values'    => array(
              array(
                  'value'     => 0,
                  'label'     => Mage::helper('afterbuycheckout')->__('deaktiviert'),
              ),

              array(
                  'value'     => 1,
                  'label'     => Mage::helper('afterbuycheckout')->__('aktiviert'),
              ),
              
          ),
      ));
      if ( Mage::getSingleton('adminhtml/session')->getAfterbuycheckoutData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getAfterbuycheckoutData());
          Mage::getSingleton('adminhtml/session')->setAfterbuycheckoutData(null);
      } elseif ( Mage::registry('afterbuycheckout_data') ) {
          $form->setValues(Mage::registry('afterbuycheckout_data')->getData());
      }
      return parent::_prepareForm();
  }
}
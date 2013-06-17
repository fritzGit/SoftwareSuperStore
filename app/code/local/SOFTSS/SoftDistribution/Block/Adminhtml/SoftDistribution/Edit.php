<?php

class SOFTSS_Softdistribution_Block_Adminhtml_Softdistribution_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'softdistribution';
        $this->_controller = 'adminhtml_softdistribution';
        
        $this->_updateButton('save', 'label', Mage::helper('softdistribution')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('softdistribution')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('softdistribution_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'softdistribution_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'softdistribution_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('softdistribution_data') && Mage::registry('softdistribution_data')->getId() ) {
            return Mage::helper('softdistribution')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('softdistribution_data')->getTitle()));
        } else {
            return Mage::helper('softdistribution')->__('Add Item');
        }
    }
}
<?php

class SOFTSS_Softd_Block_Adminhtml_Softd_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'softd';
        $this->_controller = 'adminhtml_softd';
        
        $this->_updateButton('save', 'label', Mage::helper('softd')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('softd')->__('Delete Item'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('softd_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'softd_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'softd_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('softd_data') && Mage::registry('softd_data')->getId() ) {
            return Mage::helper('softd')->__("Edit Item '%s'", $this->htmlEscape(Mage::registry('softd_data')->getTitle()));
        } else {
            return Mage::helper('softd')->__('Add Item');
        }
    }
}
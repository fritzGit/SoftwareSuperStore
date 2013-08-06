<?php

class Afterbuy_Afterbuycheckout_Block_Adminhtml_Afterbuycheckout_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'afterbuycheckout';
        $this->_controller = 'adminhtml_afterbuycheckout';
        
        $this->_updateButton('save', 'label', Mage::helper('afterbuycheckout')->__('Save Config'));
        

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('afterbuycheckout_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'afterbuycheckout_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'afterbuycheckout_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('afterbuycheckout_data') && Mage::registry('afterbuycheckout_data')->getId() ) {
            return Mage::helper('afterbuycheckout')->__("Edit Afterbuy Config '%s'", $this->htmlEscape(Mage::registry('afterbuycheckout_data')->getTitle()));
        } else {
            return Mage::helper('afterbuycheckout')->__('Add Afterbuy Config');
        }
    }
}
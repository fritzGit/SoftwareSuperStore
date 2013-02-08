<?php
/**
 * Created by jgalvez
 */
class SOFTSS_Topseller_Block_Adminhtml_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_topsellergrid';
        $this->_blockGroup = 'softsstopseller';
        $this->_mode = 'edit';

        $this->_addButton('save_and_continue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
        ), -100);

        $this->_formScripts[] = "
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";

        $this->_updateButton('save', 'label', Mage::helper('softsstopseller')->__('Save Topseller'));
    }

    protected function _prepareLayout()
    {
        if ($this->_blockGroup && $this->_controller && $this->_mode) {
            $this->setChild('form', $this->getLayout()->createBlock('softsstopseller/adminhtml_topsellergrid_edit_form'));
        }
        return parent::_prepareLayout();
    }


    public function getHeaderText()
    {
        if (Mage::registry('topseller_data') && Mage::registry('topseller_data')->getId())
        {
            return Mage::helper('softsstopseller')->__('Edit Homepage Topseller');
        } else {
            return Mage::helper('softsstopseller')->__('New Homepage Topseller');
        }
    }

}

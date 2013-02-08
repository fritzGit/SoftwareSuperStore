<?php
/**
 * Created by
 * User: jgalvez
 * @author juan galvez
 * @pagage SOFTSS
 * @subpackage SOFTSS_TeaserEditor
 */
class SOFTSS_TeaserEditor_Block_Adminhtml_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_teasergrid';
        $this->_blockGroup = 'softssteasereditor';
        $this->_mode = 'edit';

        $this->_addButton('save_and_continue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
        ), -100);
        $this->_updateButton('save', 'label', Mage::helper('softssteasereditor')->__('Save Teaser'));
        $this->_updateButton('save', 'onclick', 'saveSubmit()');

        $this->_formScripts[] = "
            document.observe('dom:loaded', function() {
                toggleCategoryDropdown();
            });

            function checkCategoryOnSubmit(){
                if($('teaser_group_id').value==5 && $('category_id').value == ''){
                    alert('You much choose a category');
                    return false;
                }
                return true;
            }

            function saveSubmit(){
                if(checkCategoryOnSubmit())
                    $('edit_form').submit();
            }

            function saveAndContinueEdit(){
                if(checkCategoryOnSubmit())
                    editForm.submit($('edit_form').action+'back/edit/');
            }

            function setTeaserGroupDropdown(){
                $('teaser_group_id').value = '5';
            }

            function toggleCategoryDropdown(){
                if($('teaser_group_id').value != 5){
                    $('category_id').value = '';
                    $('category_id').hide();
                    $(category_id).up().previous().down().hide();
                }else{
                    $('category_id').show();
                    $(category_id).up().previous().down().show();
                }
            }
        ";
    }

    protected function _prepareLayout()
    {
        if ($this->_blockGroup && $this->_controller && $this->_mode) {
            $this->setChild('form', $this->getLayout()->createBlock($this->_blockGroup . '/' . $this->_controller . '_' . $this->_mode . '_form'));
        }
        return parent::_prepareLayout();
    }


    public function getHeaderText()
    {
        if (Mage::registry('teaser_data') && Mage::registry('teaser_data')->getId())
        {
            return Mage::helper('softssteasereditor')->__('Edit %s Teaser', Mage::helper('softssteasereditor')->getTeaserGroupName(Mage::registry('teaser_data')->getTeaserGroupId()));
        } else {
            return Mage::helper('softssteasereditor')->__('New Teaser');
        }
    }

}

<?php
/**
 * User: jgalvez
 */
class SOFTSS_TeaserEditor_Block_Adminhtml_Teasergrid_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        /*
         * Checking if user have permissions to save information
         */
        /*if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }*/

        if (Mage::getSingleton('adminhtml/session')->getTeaserData())
        {
            $data = Mage::getSingleton('adminhtml/session')->getTeaserData();
            Mage::getSingleton('adminhtml/session')->getTeaserData(null);
        }
        elseif (Mage::registry('teaser_data'))
        {
            $oTeaser = Mage::registry('teaser_data');
            $data = $oTeaser->getData();
        }
        else
        {
            $data = array();
        }

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data',
        ));
        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset('fieldset', array('legend' => $this->__('Teaser'), 'class' => 'fieldset-wide'));


        if($oTeaser->getImage())
            $data['image'] = Mage::helper('softssteasereditor')->getTeaserImageDir() . $oTeaser->getImage();

        $fieldset->addField('image', 'image',
            array(
                'name' => 'teaserimage',
                'label' => $this->__('Teaser Image'),
                'title' => $this->__('Teaser Image'),
                #'disabled'  => $isElementDisabled,
            )
        );

        $fieldset->addField('title', 'text',
            array(
                'name' => 'title',
                'label' => $this->__('Teaser title'),
                'title' => $this->__('Teaser Title'),
                'maxlength' => '255',
                #'disabled'  => $isElementDisabled,
                'required'  => false,
            )
        );

        $fieldset->addField('link', 'text',
            array(
                'name' => 'link',
                'label' => $this->__('Teaser Link'),
                'title' => $this->__('Teaser Link'),
                'class' => 'validate-url-or-sku',
                'maxlength' => '255',
                #'disabled'  => $isElementDisabled,
                'required'  => false,
                'note' => $this->__('Enter SKU or URL here (ie. "2172" or "http://www.mysite.de")')
            )
        );

        $fieldset->addField('teaser_group_id', 'select',
            array(
                'name' => 'teaser_group_id',
                'label' => $this->__('Teaser group'),
                'title' => $this->__('Teaser group'),
                'values'    => $this->getTeaserGroupToOptionArray(),
                'onchange' => 'toggleCategoryDropdown()',
                #'disabled'  => $isElementDisabled,
                'required'  => true
            )
        );


        $fieldset->addField('category_id', 'select',
            array(
                'name' => 'category_id',
                'label' => $this->__('Category'),
                'title' => $this->__('Category'),
                'values'    => $this->getCategoriesToOptionArray(),
                'onchange' => 'setTeaserGroupDropdown()',
                #'disabled'  => $isElementDisabled,
                'required'  => false
            )
        );

        $fieldset->addField('sort', 'text',
            array(
                'name' => 'sort',
                'label' => $this->__('Sort'),
                'title' => $this->__('Sort'),
                'maxlength' => '5',
                #'disabled'  => $isElementDisabled,
                'required'  => false,
            )
        );

        $fieldset->addField('store_id', 'select',
            array(
                'name' => 'store_id',
                'label' => $this->__('Store ID'),
                'title' => $this->__('Store ID'),
                'values'    => $this->getStoresToOptionArray(),
                #'disabled'  => $isElementDisabled,
                'required'  => true
            )
        );

        $form->setValues($data);

        return parent::_prepareForm();
    }

    protected function getTeaserGroupToOptionArray(){
        $optionArray = array('' => '');
        $optionArray['1'] = 'Homepage Left Sidebar';
        $optionArray['2'] = 'Homepage Bottom';
        $optionArray['3'] = 'Homepage Right Sidebar';
        $optionArray['4'] = 'Homepage Slider';
        $optionArray['5'] = 'Category';

        return $optionArray;
    }

    protected function getCategoriesToOptionArray(){
        $optionArray = array('' => '');

        $_categories = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('level',array(2,3));

        foreach ($_categories as $_category) {
            $optionArray[$_category->getId()] = $_category->getName();
        }

        return $optionArray;
    }

    protected function getStoresToOptionArray(){
        $optionArray = array();

        $stores = Mage::app()->getStores();
        foreach($stores as $store){
            $optionArray[$store->getId()] = $store->getName();
        }

        return $optionArray;
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('softssteasereditor/' . $action);
    }
}

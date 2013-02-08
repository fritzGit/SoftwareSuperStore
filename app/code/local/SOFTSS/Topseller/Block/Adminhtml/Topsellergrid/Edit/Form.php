<?php
/**
 * Created by JetBrains PhpStorm.
 * User: jgalvez
 * Date: 11/10/12
 * Time: 17:36
 * To change this template use File | Settings | File Templates.
 */
class SOFTSS_Topseller_Block_Adminhtml_Topsellergrid_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('topseller');

        if (Mage::getSingleton('adminhtml/session')->getTopsellerData())
        {
            $data = Mage::getSingleton('adminhtml/session')->getTopsellerData();
            Mage::getSingleton('adminhtml/session')->getTopsellerData(null);
        }
        elseif (Mage::registry('topseller_data'))
        {
            $oTopseller = Mage::registry('topseller_data');
            $data = $oTopseller->getData();
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

        $fieldset = $form->addFieldset('fieldset', array('legend' => $this->__('Topseller'), 'class' => 'fieldset-wide'));

        $fieldset->addField('product_id', 'text',
            array(
                'name' => 'product_id',
                'label' => $this->__('Product ID'),
                'title' => $this->__('Product ID'),
                'class' => 'validate',
                'maxlength' => '20',
                'required'  => true,
                'note' => $this->__('Enter product Id (ie. "21722")')
            )
        );

        $fieldset->addField('position', 'text',
            array(
                'name' => 'position',
                'label' => $this->__('Position'),
                'title' => $this->__('Position'),
                'maxlength' => '2',
                'required'  => true,
            )
        );

        $fieldset->addField('store_id', 'select',
            array(
                'name' => 'store_id',
                'label' => $this->__('Store ID'),
                'title' => $this->__('Store ID'),
                'values'    => $this->getStoresToOptionArray(),
                'required'  => true
            )
        );

        $form->setValues($data);

        return parent::_prepareForm();
    }

    protected function getStoresToOptionArray(){
        $optionArray = array();

        $stores = Mage::app()->getStores();
        foreach($stores as $store){
            $optionArray[$store->getId()] = $store->getName();
        }

        return $optionArray;
    }
}

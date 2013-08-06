<?php

$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$aManufacturers = array('Microsoft','Symantec','Adobe','Dell','Toshiba','Fujitsu','Sharp','McAfee','Norton','Apple','Canon','Roxio');
$iProductEntityTypeId = Mage::getModel('catalog/product')->getResource()->getTypeId();
$aOption = array();
$aOption['attribute_id'] = $installer->getAttributeId($iProductEntityTypeId, 'manufacturer');

for($iCount=0;$iCount<sizeof($aManufacturers);$iCount++){
   $aOption['value']['option'.$iCount][0] = $aManufacturers[$iCount];
}
$installer->addAttributeOption($aOption);

$installer->updateAttribute('catalog_product', 'manufacturer', 'apply_to', 0);


$attrSetId = $this->getAttributeSetId('catalog_product', 'Default');
$installer->addAttributeToSet(Mage_Catalog_Model_Product::ENTITY, $attrSetId, 'General', 'manufacturer');
$installer->endSetup();
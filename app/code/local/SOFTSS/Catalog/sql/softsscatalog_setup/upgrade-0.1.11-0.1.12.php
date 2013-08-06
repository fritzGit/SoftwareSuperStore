<?php

$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();


$baseSetId = 4; // default set usually...
$attributeSets = array('windows', 'office', 'games');

foreach($attributeSets as $attributeSet) {
    
    $entityTypeId = Mage::getModel('catalog/product')
                ->getResource()->getEntityType()->getId();

    $attributeSet = Mage::getModel('eav/entity_attribute_set')
         ->setEntityTypeId($entityTypeId)
         ->setAttributeSetName($attributeSet);

    $attributeSet->validate();
    $attributeSet->save();
    
    $attributeSet
         ->initFromSkeleton($baseSetId)
         ->save();     
}

$installer->endSetup();
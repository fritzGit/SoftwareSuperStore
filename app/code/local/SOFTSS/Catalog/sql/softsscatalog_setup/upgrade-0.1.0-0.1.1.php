<?php

$installer = $this;
/** @var $installer Mage_Catalog_Model_Resource_Setup */

$installer->startSetup();

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'featured_product', array(
    'type'          => 'int',
    'label'         => 'Featured Product',
    'input'         => 'select',
    'source'        => 'eav/entity_attribute_source_boolean',
    'global'        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible'       => true,
    'used_in_product_listing' => true
));

$installer->endSetup();


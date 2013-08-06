<?php

$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->removeAttribute('catalog_product', 'softss_multiplayer');


$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY,'softss_multiplayer', array(
    'label'         => 'Multiplayer',
    'default'       => "",
    'required'      => 0,
    'unique'        => 0,
    'is_configurable' => 0,
    'type'             => 'int',
    'input'            => 'select',
    'source'           => 'eav/entity_attribute_source_boolean',
));

$installer->endSetup();
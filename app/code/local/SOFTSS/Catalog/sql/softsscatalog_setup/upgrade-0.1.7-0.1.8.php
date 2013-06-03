<?php

$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'softss_features', 'is_wysiwyg_enabled', 1);
$installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'softss_features', 'is_html_allowed_on_front', 1);

$installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'softss_system_requirements', 'is_wysiwyg_enabled', 1);
$installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'softss_system_requirements', 'is_html_allowed_on_front', 1);

$installer->endSetup();
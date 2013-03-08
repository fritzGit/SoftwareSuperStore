<?php

$installer = $this;
/** @var $installer Mage_Catalog_Model_Resource_Setup */

$installer->startSetup();

$installer->updateAttribute('catalog_product', 'featured_product', 'used_in_product_listing', '1');

$installer->endSetup();


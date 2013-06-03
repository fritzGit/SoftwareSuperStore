<?php

$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->removeAttribute('catalog_product', 'supplierProductID');

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY,'softss_supplier_product_id', array(
    'label'         => 'Supplier Product ID',
    'default'       => "",
    'required'      => 0,
    'unique'        => 0,
    'is_configurable' => 0,
    'input'         => 'text',
));

$installer->endSetup();
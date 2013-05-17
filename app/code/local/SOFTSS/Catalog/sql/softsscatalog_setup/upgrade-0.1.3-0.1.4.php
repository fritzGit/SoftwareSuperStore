<?php

$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY,'supplierProductID', array(
    'label'         => 'Suppier Product ID',
    'default'       => "",
    'required'      => 0,
    'unique'        => 0,
    'is_configurable' => 0,
    'input'         => 'text',
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY,'platform', array(
    'label'         => 'Platform',
    'default'       => "",
    'required'      => 0,
    'unique'        => 0,
    'is_configurable' => 0,
    'input'         => 'text',
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY,'systemrequirements', array(
    'label'         => 'System requirements',
    'default'       => "",
    'required'      => 0,
    'unique'        => 0,
    'is_configurable' => 0,
    'input'         => 'text',
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY,'programmversion', array(
    'label'         => 'Programm version',
    'default'       => "",
    'required'      => 0,
    'unique'        => 0,
    'is_configurable' => 0,
    'input'         => 'text',
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY,'publisher', array(
    'label'         => 'Publisher',
    'default'       => "",
    'required'      => 0,
    'unique'        => 0,
    'is_configurable' => 0,
    'input'         => 'text',
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY,'versiontype', array(
    'label'         => 'Versiontype',
    'default'       => "",
    'required'      => 0,
    'unique'        => 0,
    'is_configurable' => 0,
    'input'         => 'text',
));


$installer->endSetup();
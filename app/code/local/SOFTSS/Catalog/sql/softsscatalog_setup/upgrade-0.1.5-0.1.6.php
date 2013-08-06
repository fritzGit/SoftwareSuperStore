<?php

$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->removeAttribute('catalog_product', 'supplierProductID');
$installer->removeAttribute('catalog_product', 'systemrequirements');


$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY,'softss_mastersize', array(
    'label'         => 'Master size in MB',
    'default'       => "",
    'required'      => 0,
    'unique'        => 0,
    'is_configurable' => 0,
    'input'         => 'text',
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY,'softss_release_date_actual', array(
    'label'         => 'Release Date Actual',
    'default'       => "",
    'required'      => 0,
    'unique'        => 0,
    'is_configurable' => 0,
    'type'            => 'datetime',
    'input'           => 'date',
    'backend'         => 'eav/entity_attribute_backend_datetime',

));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY,'softss_serialnumber_required', array(
    'label'         => 'Serialnumber Required',
    'default'       => "",
    'required'      => 0,
    'unique'        => 0,
    'is_configurable' => 0,
    'type'             => 'int',
    'input'            => 'select',
    'source'           => 'eav/entity_attribute_source_boolean',
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY,'softss_extendedorder_required', array(
    'label'         => 'Extended Order Required',
    'default'       => "",
    'required'      => 0,
    'unique'        => 0,
    'is_configurable' => 0,
    'type'             => 'int',
    'input'            => 'select',
    'source'           => 'eav/entity_attribute_source_boolean',
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY,'softss_backup_cd_available', array(
    'label'         => 'Backup CD Available',
    'default'       => "",
    'required'      => 0,
    'unique'        => 0,
    'is_configurable' => 0,
    'type'             => 'int',
    'input'            => 'select',
    'source'           => 'eav/entity_attribute_source_boolean',

));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY,'softss_system_requirements', array(
    'label'         => 'System requirements',
    'default'       => "",
    'required'      => 0,
    'unique'        => 0,
    'is_configurable' => 0,
    'type'         => 'text',
    'is_wysiwyg_enabled' => 1,
    'is_html_allowed_on_front' => 1,
    'input'    => 'textarea',
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY,'softss_features', array(
    'label'         => 'Features',
    'default'       => "",
    'required'      => 0,
    'unique'        => 0,
    'is_configurable' => 0,
    'type'         => 'text',
    'is_wysiwyg_enabled' => 1,
    'is_html_allowed_on_front' => 1,
    'input'    => 'textarea',

));

$installer->endSetup();
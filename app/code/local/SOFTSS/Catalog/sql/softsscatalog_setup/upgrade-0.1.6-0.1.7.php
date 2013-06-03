<?php

$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();


$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY,'softss_drm_type', array(
    'label'         => 'DRM type',
    'default'       => "",
    'required'      => 0,
    'unique'        => 0,
    'is_configurable' => 0,
    'input'         => 'text',
));


$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY,'softss_multiplayer', array(
    'label'         => 'Multiplayer',
    'default'       => "",
    'required'      => 0,
    'unique'        => 0,
    'is_configurable' => 0,
    'input'         => 'text',
));

$installer->endSetup();
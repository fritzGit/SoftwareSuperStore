<?php

$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY,'softss_download_url', array(
    'label'         => 'Download Url',
    'default'       => "",
    'required'      => 0,
    'unique'        => 0,
    'is_configurable' => 0,
    'input'         => 'text',
));



$installer->endSetup();
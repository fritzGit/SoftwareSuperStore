<?php

$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->removeAttribute('catalog_product', 'softss_multiplayer');


$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY,'softss_video', array(
    'label'         => 'Product Video',
    'default'       => "",
    'required'      => 0,
    'unique'        => 0,
    'note'          => 'Video path media/video/',
    'is_configurable' => 0,
    'input'         => 'text',
));

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY,'softss_marketing_claims', array(
    'label'         => 'Marketing Claims',
    'default'       => "",
    'required'      => 0,
    'unique'        => 0,
    'is_configurable' => 0,
    'type'         => 'text',
    'is_wysiwyg_enabled' => 1,
    'is_html_allowed_on_front' => 1,
    'input'    => 'textarea',

));


$installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'softss_marketing_claims', 'is_wysiwyg_enabled', 1);
$installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'softss_marketing_claims', 'is_html_allowed_on_front', 1);

$installer->endSetup();
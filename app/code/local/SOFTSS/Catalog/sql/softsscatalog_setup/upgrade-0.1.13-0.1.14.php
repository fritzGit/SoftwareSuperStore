<?php

/*
 *  Software property of Pcfritz.de. Copyright 2013.
 */

$installer = $this;
$installer->startSetup();


$installer->updateAttribute('catalog_product','softss_release_date_actual','is_visible_on_front',1);
$installer->updateAttribute('catalog_product','softss_mastersize','is_visible_on_front',1);
$installer->updateAttribute('catalog_product','softss_backup_cd_available','is_visible_on_front',1);
$installer->updateAttribute('catalog_product','softss_system_requirements','is_visible_on_front',1);
$installer->updateAttribute('catalog_product','softss_features','is_visible_on_front',1);
$installer->updateAttribute('catalog_product','softss_multiplayer','is_visible_on_front',1);
$installer->updateAttribute('catalog_product','softss_marketing_claims','is_visible_on_front',1);
$installer->updateAttribute('catalog_product','programmversion','is_visible_on_front',1);
$installer->updateAttribute('catalog_product','publisher','is_visible_on_front',1);
$installer->updateAttribute('catalog_product','versiontype','is_visible_on_front',1);
$installer->updateAttribute('catalog_product','platform','is_visible_on_front',1);


$installer->endSetup();
?>
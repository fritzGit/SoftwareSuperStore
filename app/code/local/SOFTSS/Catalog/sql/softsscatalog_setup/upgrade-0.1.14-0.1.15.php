<?php

/*
 *  Software property of Pcfritz.de. Copyright 2013.
 */

$installer = $this;
$installer->startSetup();


$installer->updateAttribute('catalog_product','manufacturer','is_visible_on_front',1);
$installer->updateAttribute('catalog_product','language_version','is_visible_on_front',1);
$installer->updateAttribute('catalog_product','service_pack','is_visible_on_front',1);

$installer->endSetup();
?>
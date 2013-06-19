<?php

$installer = $this;
/** @var $installer Mage_Sales_Model_Resource_Setup */

$installer->startSetup();

$installer->addAttribute('order', 'softss_afterbuy_synced', array('type' => 'int', 'visible' => false, 'default' => 0));


$installer->endSetup();
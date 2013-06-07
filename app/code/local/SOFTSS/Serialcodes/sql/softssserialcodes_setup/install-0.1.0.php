<?php

$installer = $this;
/** @var $installer Mage_Sales_Model_Resource_Setup */

$installer->startSetup();

$installer->addAttribute('order', 'softss_has_serialcode', array('type' => 'int', 'visible' => false, 'default' => 0));
$installer->addAttribute('order', 'softss_serialcode_sent', array('type' => 'int', 'visible' => false, 'default' => 0));


$installer->endSetup();
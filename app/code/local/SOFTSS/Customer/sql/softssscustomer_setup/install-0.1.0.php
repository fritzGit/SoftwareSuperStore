<?php

$installer = $this;

$installer->startSetup();

/* @var $this Mage_Customer_Model_Resource_Setup */
$this->updateAttribute('customer_address','telephone','is_required','false');

$installer->endSetup();
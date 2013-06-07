<?php

$installer = $this;
/** @var $installer Mage_Sales_Model_Resource_Setup */

$installer->startSetup();

$installer->addAttribute('order', 'softss_transaction_id', array(
            'group'             => 'General',
            'label'             => 'Softdistribution Transaction ID',
            'note'              => '',
            'type'              => 'varchar',   
            'input'             => 'text'
    ));

$installer->endSetup();


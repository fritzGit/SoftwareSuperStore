<?php

$installer = $this;


$installer->startSetup();

/**
 * Create table 'softsstopseller/homepagetopseller'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('softsstopseller/homepagetopseller'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'ID')
    ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Product ID')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Position')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 1
    ), 'Store ID')
    ->setComment('Homepage Topseller Table');

$installer->getConnection()->createTable($table);

$installer->endSetup();



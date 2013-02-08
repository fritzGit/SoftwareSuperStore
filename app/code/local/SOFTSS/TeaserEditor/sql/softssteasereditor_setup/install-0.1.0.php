<?php

$installer = $this;

$installer->startSetup();

/**
 * Create table 'softssteasereditor/teaser_group'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('softssteasereditor/teaser_group'))
    ->addColumn('teaser_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
    'identity'  => true,
    'unsigned'  => true,
    'nullable'  => false,
    'primary'   => true,
), 'Teaser Group ID')
    ->addColumn('teaser_group_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable'  => false,
    'default'   => '',
), 'Image File Name')
    ->setComment('Teaser Group Table');

$installer->getConnection()->createTable($table);


$table = $installer->getConnection()
    ->newTable($installer->getTable('softssteasereditor/teaser'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Teaser ID')
    ->addColumn('teaser_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Teaser Group ID')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
    'nullable'  => false,
    'default'   => '',
    ), 'Image Title')
    ->addColumn('image', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        'default'   => '',
    ), 'Image File Name')
    ->addColumn('link', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
        'default'   => '',
    ), 'SKU or web link')
    ->addColumn('sort', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => true,
    ), 'Sort')
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => true,
    ), 'Category ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => 1
    ), 'Store ID')
    ->setComment('Teaser Table');

$installer->getConnection()->createTable($table);

$installer->endSetup();
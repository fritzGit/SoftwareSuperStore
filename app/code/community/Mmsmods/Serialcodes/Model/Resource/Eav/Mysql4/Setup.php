<?php
/*
* ModifyMage Solutions (http://ModifyMage.com)
* Serial Codes - Serial Numbers, Product Codes, PINs, and More
*
* NOTICE OF LICENSE
* This source code is owned by ModifyMage Solutions and distributed for use under the
* provisions, terms, and conditions of our Commercial Software License Agreement which
* is bundled with this package in the file LICENSE.txt. This license is also available
* through the world-wide-web at this URL: http://www.modifymage.com/software-license
* If you do not have a copy of this license and are unable to obtain it through the
* world-wide-web, please email us at license@modifymage.com so we may send you a copy.
*
* @category		Mmsmods
* @package		Mmsmods_Serialcodes
* @author		David Upson
* @copyright	Copyright 2012 by ModifyMage Solutions
* @license		http://www.modifymage.com/software-license
*/

class Mmsmods_Serialcodes_Model_Resource_Eav_Mysql4_Setup extends Mage_Eav_Model_Entity_Setup
{
    public function getDefaultEntities()
    {
        return array(
            'catalog_product' => array(
                'entity_model'      			=> 'catalog/product',
                'attribute_model'   			=> 'catalog/resource_eav_attribute',
                'table'             			=> 'catalog/product',
				'additional_attribute_table'	=> 'catalog/eav_attribute',
				'entity_attribute_collection'	=> 'catalog/product_attribute_collection',
                'attributes'        			=> array(
                    'serial_code_serialized' => array(
						'group'             => 'Serial Codes',
						'type'              => 'int',
                        'backend'           => '',
                        'frontend'          => '',
                        'label'             => 'Serial Codes Enabled',
                        'input'             => 'select',
                        'class'             => '',
                        'source'            => 'eav/entity_attribute_source_boolean',
                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'visible'           => true,
                        'required'          => true,
                        'user_defined'      => true,
                        'default'           => '0',
                        'searchable'        => false,
                        'filterable'        => false,
                        'comparable'        => false,
                        'visible_on_front'  => false,
						'sort_order'		=> 1,
                        'unique'            => false,
						'is_configurable'	=> false,
						'note'				=> 'Enables automatic issuing of codes to order on successful checkout.'
					),
                    'serial_code_pool' => array(
						'group'             => 'Serial Codes',
						'type'              => 'varchar',
                        'backend'           => '',
                        'frontend'          => '',
                        'label'             => 'Code Pool (SKU)',
                        'input'             => 'text',
                        'class'             => '',
                        'source'            => '',
                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'visible'           => true,
                        'required'          => false,
                        'user_defined'      => true,
                        'default'           => '',
                        'searchable'        => false,
                        'filterable'        => false,
                        'comparable'        => false,
                        'visible_on_front'  => false,
                        'sort_order'		=> 2,
                        'unique'            => false,
						'note'				=> 'Leave empty to use product SKU.'
					),
                    'serial_code_type' => array(
						'group'             => 'Serial Codes',
						'type'              => 'varchar',
                        'backend'           => '',
                        'frontend'          => '',
                        'label'             => 'Serial Code Type',
                        'input'             => 'text',
                        'class'             => '',
                        'source'            => '',
                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'visible'           => true,
                        'required'          => false,
                        'user_defined'      => true,
                        'default'           => '',
                        'searchable'        => false,
                        'filterable'        => false,
                        'comparable'        => false,
                        'visible_on_front'  => false,
						'sort_order'		=> 3,
                        'unique'            => false,
						'note'				=> 'This field is REQUIRED for proper operation (e.g. Serial Number, Username | Password, Activation Key).'
					),
                    'serial_code_not_available' => array(
						'group'             => 'Serial Codes',
						'type'              => 'varchar',
                        'backend'           => '',
                        'frontend'          => '',
                        'label'             => 'Not Available Message',
                        'input'             => 'text',
                        'class'             => '',
                        'source'            => '',
                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'visible'           => true,
                        'required'          => false,
                        'user_defined'      => true,
                        'default'           => 'Oops! Please call or email.',
                        'searchable'        => false,
                        'filterable'        => false,
                        'comparable'        => false,
                        'visible_on_front'  => false,
						'sort_order'		=> 4,
                        'unique'            => false,
						'note'				=> 'Message displayed to customer when no codes are available to issue.'
					),
                    'serial_code_show_order' => array(
						'group'             => 'Serial Codes',
						'type'              => 'int',
                        'backend'           => '',
                        'frontend'          => '',
                        'label'             => 'Show on Customer Order',
                        'input'             => 'select',
                        'class'             => '',
                        'source'            => 'eav/entity_attribute_source_boolean',
                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'visible'           => true,
                        'required'          => true,
                        'user_defined'      => true,
                        'default'           => '0',
                        'searchable'        => false,
                        'filterable'        => false,
                        'comparable'        => false,
                        'visible_on_front'  => false,
						'sort_order'		=> 5,
                        'unique'            => false,
						'is_configurable'	=> false,
						'note'				=> 'Display codes on order in frontend for registered customers.'
					),
                    'serial_code_send_email' => array(
						'group'             => 'Serial Codes',
						'type'              => 'int',
                        'backend'           => '',
                        'frontend'          => '',
                        'label'             => 'Send Customer Email',
                        'input'             => 'select',
                        'class'             => '',
                        'source'            => 'eav/entity_attribute_source_boolean',
                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'visible'           => true,
                        'required'          => true,
                        'user_defined'      => true,
                        'default'           => '0',
                        'searchable'        => false,
                        'filterable'        => false,
                        'comparable'        => false,
                        'visible_on_front'  => false,
						'sort_order'		=> 6,
                        'unique'            => false,
						'is_configurable'	=> false,
						'note'				=> 'Automatically deliver codes to customer at checkout.'
					),
                    'serial_code_email_template' => array(
						'group'             => 'Serial Codes',
						'type'              => 'varchar',
                        'backend'           => '',
                        'frontend'          => '',
                        'label'             => 'Delivery Email Template',
                        'input'             => 'select',
                        'class'             => '',
                        'source'            => 'serialcodes/product_email_templates',
                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'visible'           => true,
                        'required'          => true,
                        'user_defined'      => true,
                        'default'           => 'serialcodes_delivery',
                        'searchable'        => false,
                        'filterable'        => false,
                        'comparable'        => false,
                        'visible_on_front'  => false,
						'sort_order'		=> 7,
                        'unique'            => false,
						'is_configurable'	=> false,
						'note'				=> 'Template to use for delivering codes to customer.'
					),
                    'serial_code_email_type' => array(
						'group'             => 'Serial Codes',
						'type'              => 'varchar',
                        'backend'           => '',
                        'frontend'          => '',
                        'label'             => 'Email Type',
                        'input'             => 'text',
                        'class'             => '',
                        'source'            => '',
                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'visible'           => true,
                        'required'          => false,
                        'user_defined'      => true,
                        'default'           => '',
                        'searchable'        => false,
                        'filterable'        => false,
                        'comparable'        => false,
                        'visible_on_front'  => false,
						'sort_order'		=> 8,
                        'unique'            => false,
						'note'				=> 'Displayed in checkout success message and optionally within delivery email (e.g. Instructions, Warranty, Rules)'
					),
                    'serial_code_send_copy' => array(
						'group'             => 'Serial Codes',
						'type'              => 'varchar',
                        'backend'           => '',
                        'frontend'          => '',
                        'label'             => 'Email Blind Copy To',
                        'input'             => 'text',
                        'class'             => '',
                        'source'            => '',
                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'visible'           => true,
                        'required'          => false,
                        'user_defined'      => true,
                        'default'           => '',
                        'searchable'        => false,
                        'filterable'        => false,
                        'comparable'        => false,
                        'visible_on_front'  => false,
						'sort_order'		=> 9,
                        'unique'            => false,
						'note'				=> 'Separate each provided email address with spaces (do not use commas, semicolons, or other delimiters).'
					),
                    'serial_code_warning_level' => array(
						'group'             => 'Serial Codes',
						'type'              => 'int',
                        'backend'           => '',
                        'frontend'          => '',
                        'label'             => 'Low Warning Level',
                        'input'             => 'text',
                        'class'             => '',
                        'source'            => '',
                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'visible'           => true,
                        'required'          => false,
                        'user_defined'      => true,
                        'default'           => '0',
                        'searchable'        => false,
                        'filterable'        => false,
                        'comparable'        => false,
                        'visible_on_front'  => false,
						'sort_order'		=> 10,
                        'unique'            => false,
						'note'				=> 'Notify by email when remaining codes reaches this number.'
					),
                    'serial_code_warning_template' => array(
						'group'             => 'Serial Codes',
						'type'              => 'varchar',
                        'backend'           => '',
                        'frontend'          => '',
                        'label'             => 'Low Warning Email Template',
                        'input'             => 'select',
                        'class'             => '',
                        'source'            => 'serialcodes/product_email_templates',
                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'visible'           => true,
                        'required'          => true,
                        'user_defined'      => true,
                        'default'           => 'serialcodes_warning',
                        'searchable'        => false,
                        'filterable'        => false,
                        'comparable'        => false,
                        'visible_on_front'  => false,
						'sort_order'		=> 11,
                        'unique'            => false,
						'is_configurable'	=> false,
						'note'				=> 'Template to use for warning.'
					),
                    'serial_code_send_warning' => array(
						'group'             => 'Serial Codes',
						'type'              => 'varchar',
                        'backend'           => '',
                        'frontend'          => '',
                        'label'             => 'Email Low Warning To',
                        'input'             => 'text',
                        'class'             => '',
                        'source'            => '',
                        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                        'visible'           => true,
                        'required'          => false,
                        'user_defined'      => true,
                        'default'           => '',
                        'searchable'        => false,
                        'filterable'        => false,
                        'comparable'        => false,
                        'visible_on_front'  => false,
						'sort_order'		=> 12,
                        'unique'            => false,
						'note'				=> 'Separate each provided email address with spaces (do not use commas, semicolons, or other delimiters).'
					)
				)
			),
			'order_item' => array(
                'entity_model'      => 'sales/order_item',
                'table'             => 'sales/order_entity',
                'attributes'        => array(
                    'serial_code_type' => array(
                        'type'              => 'varchar',
                        'label'             => 'Serial Code Type',
                        'visible'           => true,
                        'required'          => false,
                        'user_defined'      => true
					),
                     'serial_codes' => array(
                        'type'              => 'text',
                        'label'             => 'Serial Codes',
                        'visible'           => true,
                        'required'          => false,
                        'user_defined'      => true
					)
				)
			)
		);
	}
}
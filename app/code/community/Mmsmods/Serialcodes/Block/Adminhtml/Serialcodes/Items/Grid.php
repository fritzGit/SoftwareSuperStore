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
 
class Mmsmods_Serialcodes_Block_Adminhtml_Serialcodes_Items_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('serialcode_itemsGrid');
		$this->setDefaultSort('item_id');
		$this->setSaveParametersInSession(true);
    }
 
	protected function _prepareCollection()
	{
		$ordertable = Mage::getSingleton('core/resource')->getTableName('sales/order');
		$itemtable = Mage::getSingleton('core/resource')->getTableName('sales/order_item');
		$collection = Mage::getModel('sales/order_item')->getCollection();
		$collection	->getSelect()
					->join($ordertable,"main_table.order_id=$ordertable.entity_id", array('increment_id','store_id'));
		$this->addCustomerName($collection, $ordertable);
		$ids = $this->getConfigurableChildrenIds();
		if(!empty($ids)) {
			$collection->addFieldToFilter('main_table.item_id',array("nin"=>$ids));
		}
		$collection->setOrder('increment_id', 'DESC');
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}
 
    protected function _prepareColumns()
    {		
		$ordertable = Mage::getSingleton('core/resource')->getTableName('sales/order');
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'    		=> Mage::helper('serialcodes')->__('Store'),
				'align'     		=>'left',
				'width'     		=> '160px',
                'index'     		=> 'store_id',
				'filter_index'		=> $ordertable.'.store_id',
                'type'      		=> 'store',
                'store_view'		=> true,
                'display_deleted'	=> true
            ));
        }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
			$this->addColumn('increment_id', array(
				'header'    => Mage::helper('serialcodes')->__('Order'),
				'align'     =>'left',
				'width'     => '80px',
				'index'     => 'increment_id',
				'renderer'	=> 'Mmsmods_Serialcodes_Block_Adminhtml_Serialcodes_Items_Renderer_Order'
			));
		} else {
			$this->addColumn('increment_id', array(
				'header'    => Mage::helper('serialcodes')->__('Order'),
				'align'     =>'left',
				'width'     => '80px',
				'index'     => 'increment_id',
			));
		}

        $this->addColumn('qty_ordered', array(
            'header'    => Mage::helper('serialcodes')->__('Qty'),
            'align'     =>'left',
            'width'     => '30px',
            'index'     => 'qty_ordered'
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('serialcodes')->__('Product'),
            'align'     =>'left',
            'width'     => '180px',
            'index'     => 'name'
        ));

        if (Mage::getSingleton('admin/session')->isAllowed('customer/manage')) {
			$this->addColumn('customer_name', array(
				'header'    => Mage::helper('serialcodes')->__('Customer'),
				'align'     =>'left',
				'width'     => '120px',
				'index'     => 'customer_id',
				'format'    => '$fullname',
				'filter_condition_callback' => array($this, '_customerNameCondition'),
				'renderer'	=> 'Mmsmods_Serialcodes_Block_Adminhtml_Serialcodes_Items_Renderer_Customer'
			));
		} else {
			$this->addColumn('customer_name', array(
				'header'    => Mage::helper('serialcodes')->__('Customer'),
				'align'     =>'left',
				'width'     => '120px',
				'index'     => 'customer_id',
				'format'    => '$fullname',
				'filter_condition_callback' => array($this, '_customerNameCondition'),
			));
		}
		
		$this->addColumn('serial_code_type', array(
            'header'    => Mage::helper('serialcodes')->__('Serial Code Type'),
            'align'     => 'left',
            'width'     => '160px',
            'index'     => 'serial_code_type'
        ));

        $this->addColumn('serial_codes', array(
            'header'    => Mage::helper('serialcodes')->__('Serial Codes'),
            'align'     =>'left',
            'width'     => '160px',
            'index'     => 'serial_codes',
			'renderer'	=> 'Mmsmods_Serialcodes_Block_Adminhtml_Serialcodes_Items_Renderer_Codes'
        ));

		$this->addColumn('created_at', array(
            'header'    => Mage::helper('serialcodes')->__('Created'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'index'     => 'created_at'
        ));

        $this->addColumn('updated_at', array(
            'header'    => Mage::helper('serialcodes')->__('Updated'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'index'     => 'updated_at'
        ));   

        return parent::_prepareColumns();
    }

	public function getRowUrl($row)
    {
		if(Mage::getSingleton('admin/session')->isAllowed('sales/serialcodes_items/serialcodes_items_edit'))
		{
			return $this->getUrl('*/*/edit', array(
				'id' 		=> $row->getId(),
				'order'		=> $row->getIncrementId(),
				'qty'		=> $row->getQtyOrdered(),
				'customer'	=> $row->getFullname()
			));
		}
    }
	
	private function addCustomerName($collection, $ordertable)
	{
		if(version_compare(Mage::getVersion(),'1.4.1.1') >= 0)
		{
			$collection	->getSelect()
						->columns(new Zend_Db_Expr("CONCAT(`$ordertable`.`customer_firstname`, ' ',`$ordertable`.`customer_lastname`) AS fullname"));
		} else {
			$itemtable = Mage::getSingleton('core/resource')->getTableName('sales/quote_item');
			$addresstable = Mage::getSingleton('core/resource')->getTableName('sales/quote_address');
			$collection	->getSelect()
						->join($itemtable,"main_table.quote_item_id=$itemtable.item_id",array());
			$collection	->getSelect()
						->join($addresstable,"$itemtable.quote_id = $addresstable.quote_id",array('firstname', 'lastname'))
						->where($addresstable.'.address_type = ?', 'billing');
			$collection	->getSelect()
						->columns(new Zend_Db_Expr("CONCAT(`$addresstable`.`firstname`, ' ',`$addresstable`.`lastname`) AS fullname"));
		}
	}

    protected function _customerNameCondition($collection, $column)
	{
        if (!$value = trim($column->getFilter()->getValue())) {
            return;
        }
		$condition = $column->getFilter()->getCondition();
		$filter = $condition['like'];
		$filter = str_replace("'","",$filter);
		$filters = explode(' ',$filter);
		$filters = array_map('trim', $filters);
		$filters = array_filter($filters);
		$filters = array_values($filters);
		if(version_compare(Mage::getVersion(),'1.4.1.1') >= 0)
		{
			$table = Mage::getSingleton('core/resource')->getTableName('sales/order');
			if(count($filters) == 2)
			{
				$collection->getSelect()->where("$table.customer_firstname LIKE '$filters[0]%' AND $table.customer_lastname LIKE '%$filters[1]'");
			} else {
				$collection->getSelect()->where("$table.customer_firstname LIKE '$filter' OR $table.customer_lastname LIKE '$filter'");
			}
		} else {
			$table = Mage::getSingleton('core/resource')->getTableName('sales/quote_address');
			if(count($filters) == 2)
			{
				$collection->getSelect()->where("$table.firstname LIKE '$filters[0]%' AND $table.lastname LIKE '%$filters[1]'");
			} else {
				$collection->getSelect()->where("$table.firstname LIKE '$filter' OR $table.lastname LIKE '$filter'");
			}
		}
		return $this;
    }

	private function getConfigurableChildrenIds()
	{
		$table = Mage::getSingleton('core/resource')->getTableName('sales/order_item');
		$items = Mage::getModel('sales/order_item')->getCollection();
		$items	->getSelect()
				->joinLeft($table,"main_table.parent_item_id = $table.item_id",array())
				->where($table.'.product_type = ?', 'configurable');

		return $items->getColumnValues('item_id');
	}
}
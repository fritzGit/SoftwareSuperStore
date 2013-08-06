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

abstract class Mmsmods_Serialcodes_Block_Sales_Items_Abstract extends Mage_Sales_Block_Items_Abstract
{
	public function getItemHtml(Varien_Object $item)
	{
		$html = parent::getItemHtml($item);
		$product = Mage::getModel('catalog/product')->load($item->getProductId());
		$show = 0;
		if($item->getProductType() == 'configurable')
		{
			$itemId = $item->getItemId();
			$children = Mage::getModel('sales/order_item')->getCollection();
			$children->getSelect()->where("main_table.parent_item_id = $itemId");
			foreach($children as $child)
			{
				$show = $show | Mage::getModel('catalog/product')->load($child->getProductId())->getSerialCodeShowOrder();
			}
		}
		if($show || $product->getSerialCodeShowOrder())
		{
			$name = $this->htmlEscape($product->getName());
			$codetype = $item->getSerialCodeType();
			$codes = explode("\n",$item->getSerialCodes());
			$local = '<span style="font-weight:normal;">';
			foreach($codes as $code)
			{
				$local .= '</br>'.$codetype.': '.$code;
			}
			$local .= '</span>';
			$start = strpos($html,$name) + strlen($name);
			$test = trim(strip_tags($local));
			if($test && $test <> ':') {$html = substr_replace($html,$local,$start,0);}
		}
		return $html;
	}
}
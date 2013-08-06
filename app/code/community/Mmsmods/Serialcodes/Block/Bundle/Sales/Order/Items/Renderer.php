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

class Mmsmods_Serialcodes_Block_Bundle_Sales_Order_Items_Renderer extends Mage_Bundle_Block_Sales_Order_Items_Renderer
{
	public function getValueHtml($item)
	{
		$html = parent::getValueHtml($item);
		$product = Mage::getModel('catalog/product')->load($item->getProductId());
		if($product->getSerialCodeShowOrder())
		{
			$name = $product->getName();
			$codetype = $item->getSerialCodeType();
			$codes = explode("\n",$item->getSerialCodes());
			$local = '';
			foreach($codes as $code)
			{
				$local .= '</br>'.$codetype.': '.$code;
			}
			$test = trim(strip_tags($local));
			if($test && $test <> ':') {$html = $html . $local;}
		}
		return $html;
	}
}
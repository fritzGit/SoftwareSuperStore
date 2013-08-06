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

class Mmsmods_Serialcodes_Model_Observer extends Mage_Core_Controller_Varien_Action
{
    public function __construct()
    {
    }

	public function addCodesToOrder($observer)
	{
		$session = Mage::getSingleton('checkout/session');
		$order = Mage::getSingleton('sales/order')->load($session->getLastOrderId());
		$storeid = $order->getStoreId();
		$orderId = $order->getIncrementId();
		$status = $order->getStatus();
		$payment = $order->getPayment()->getMethodInstance()->getCode();
		$items = $order->getAllItems();
		$templatearray = array();
		$saved = 0;
		foreach ($items as $item)
		{
			$product = Mage::getModel('catalog/product')->setStoreId($storeid)->load($item->getProductId());
			if ($product->getSerialCodeSerialized())
			{
				if($parent = $item->getParentItem())
				{
					if($parent->getProductType() == 'configurable' && !Mage::getModel('catalog/product')->load($parent->getProductId())->getSerialCodeSerialized())
					{
						$item = $parent;
					}
				}
				$send = $product->getSerialCodeSendEmail();
				$template = $product->getSerialCodeEmailTemplate();
				$templatearray[$template]['emailtype'] = $product->getSerialCodeEmailType();
				$templatearray[$template]['codetype'] = 'Serial Code';
				$templatearray[$template]['bcc'] = $product->getSerialCodeSendCopy();
				if(empty($templatearray[$template]['html']) && $send) {$templatearray[$template]['html'] = '<div class="sc_items">';}
				$qty = round($item->getQtyOrdered());
				if('' == $sku = trim($product->getSerialCodePool())) {$sku = trim($product->getSku());}
				$codes = Mage::getSingleton('serialcodes/serialcodes')->getCollection()->addFieldToFilter('sku',array('like' => $sku))->load();
				if($send)
				{
					if ($templatearray[$template]['html'] != '<div class="sc_items">') {$templatearray[$template]['html'] .= '<br />';}
					$templatearray[$template]['html'] .= '<span class="sc_product">'.$product->getName().'</span><br />';
				}
				for ($i=1; $i<=$qty; $i++)
				{
					$saved = 0;
					if($product->getSerialCodeType()) {$templatearray[$template]['codetype'] = trim($product->getSerialCodeType());}
					foreach ($codes as $code)
					{
						$item->setSerialCodeType($templatearray[$template]['codetype']);
						if ($status == 'payment_review' || 
							$status == 'pending_payment' || 
							$status == 'pending_paypal' || 
							$status == 'fraud' || 
							$status == 'holded' || 
							$payment == 'checkmo' || 
							$payment == 'cashondelivery' || 
							$payment == 'banktransfer' || 
							$payment == 'purchaseorder' || 
							$payment == 'directdeposit_au' || 
							$payment == 'msp_banktransfer' || 
							$payment == 'msp_directdebit')
						{
							$pcode = $item->getSerialCodes();
							if ($pcode){$pcode .= "\n";}
							$item->setSerialCodes($pcode.Mage::helper('serialcodes')->__('Issued when payment received.'));
							$item->save();
							if($send) {$templatearray[$template]['html'] .= '<span class="sc_type">'.$item->getSerialCodeType().':</span> <span class="sc_code">'.Mage::helper('serialcodes')->__('Issued when payment received.').'</span><br />';}
							$saved = 1;
							break;
						} else {
							if ($code->getStatus() == 0)
							{
								$pcode = $item->getSerialCodes();
								if ($pcode){$pcode .= "\n";}
								$item->setSerialCodes($pcode.$code->getCode());
								$item->save();
								$code->setStatus(1);
								$code->setNote($orderId);
								$code->setUpdateTime(now());
								$code->save();
								if($send) {$templatearray[$template]['html'] .= '<span class="sc_type">'.$item->getSerialCodeType().':</span> <span class="sc_code">'.$code->getCode().'</span><br />';}
								$saved = 1;
								break;
							}
						}
					}
					if (!$saved)
					{
						$pcode = $item->getSerialCodes();
						if ($pcode){$pcode .= "\n";}
						if(!trim($message = $product->getSerialCodeNotAvailable())) {$message = Mage::helper('serialcodes')->__('Oops! Not available.');}
						$item->setSerialCodeType($templatearray[$template]['codetype']);
						$item->setSerialCodes($pcode.$message);
						if($send) {$templatearray[$template]['html'] .= '<span class="sc_type">'.$item->getSerialCodeType().':</span> <span class="sc_code">'.$message.'</span><br />';}
						$item->save();
						$saved = 1;
					}
				}
			}
			if($saved && $email = $product->getData('serial_code_send_warning'))
			{
				$level = $product->getData('serial_code_warning_level');
				$available = count(Mage::getSingleton('serialcodes/serialcodes')->getCollection()->addFieldToFilter('sku',array('like' => $sku))->addFieldToFilter('status',array('like' => 0))->load());
				if($available <= $level)
				{
					$emailvars = array(
						'product'	=> $product->getName(),
						'available'	=> $available,
						'none'		=> !$available,
						'codetype'	=> $templatearray[$template]['codetype'],
						'pool'		=> $sku,
						'order'		=> $order
					);
					if(is_numeric($template = $product->getData('serial_code_warning_template')))
					{
						$emailTemplate = Mage::getModel('core/email_template')->load($template);
					} else {
						$emailTemplate = Mage::getModel('core/email_template')->loadDefault($template);
					}
					$emails = explode(' ',$email);
					$emails = array_map('trim', $emails);
					$emails = array_filter($emails);
					$email = $emails[0];
					unset($emails[0]);
					$emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_sales/name'));
					$emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_sales/email'));
					if ($emails = array_values($emails)) {$emailTemplate->addBcc($emails);}
					$emailTemplate->send(
						$email,
						'Administrator',
						$emailvars);
				}
			}
		}
		if(!empty($templatearray))
		{
			foreach ($templatearray as $template => $value)
			{
				if(isset($value['html']))
				{
					$value['html'] .= '</div>';
					$itemstext = strip_tags(str_replace('<br />',"\n",$value['html']));
					if (is_numeric($template))
					{
						$emailTemplate = Mage::getModel('core/email_template')->load($template);
					} else {
						$emailTemplate = Mage::getModel('core/email_template')->loadDefault($template);
					}
					$emailvars = array(
						'itemstext'	=> $itemstext,
						'itemshtml'	=> $value['html'],
						'codetype'	=> $value['codetype'],
						'emailtype'	=> $value['emailtype'],
						'order'		=> $order
					);
					$emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_sales/name'));
					$emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_sales/email'));
					if($bcc = $value['bcc'])
					{
						$emails = explode(' ',$bcc);
						$emails = array_map('trim', $emails);
						$emails = array_filter($emails);
						$emailTemplate->addBcc($emails);
					}
					$emailTemplate->send(
						$order->getCustomerEmail(),
						$order->getBillingAddress()->getName(),
						$emailvars);
					if($value['emailtype'])
					{
						Mage::getSingleton('checkout/session')
							->addNotice(Mage::helper('serialcodes')
							->__('Your %s(s) and %s have been emailed to %s.',$value['codetype'], $value['emailtype'], $order->getCustomerEmail()));
					} else {
						Mage::getSingleton('checkout/session')
							->addNotice(Mage::helper('serialcodes')
							->__('Your %s(s) have been emailed to %s.',$value['codetype'], $order->getCustomerEmail()));
					}
					$this->_initLayoutMessages('checkout/session');
				}
			}
		}
	}

	public function disableSerialCodeAttributes($observer)
	{
		if(Mage::app()->getRequest()->getActionName() == 'edit' || Mage::app()->getRequest()->getParam('type'))
		{
			$attributes = $observer->getEvent()->getProduct()->getAttributes();
			foreach($attributes as $attribute)
			{
				if(strpos($attribute->getAttributeCode(), 'serial_code') !== FALSE)
				{
					if(Mage::getSingleton('admin/session')->isAllowed('catalog/serialcodes_attributes'))
					{
						$attribute->setIsVisible(1);
					} else {
						$attribute->setIsVisible(0);
					}
				}
			}
		}
	}
}
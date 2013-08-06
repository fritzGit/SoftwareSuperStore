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

class Mmsmods_Serialcodes_Adminhtml_Serialcodes_ItemsController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('sales/serialcodes_items')
            ->_addBreadcrumb(Mage::helper('serialcodes')->__('Serial Code Items'),Mage::helper('serialcodes')->__('Serial Code Items'));
        return $this;
    }   

    public function indexAction() {
        $this->_initAction();       
        $this->_addContent($this->getLayout()->createBlock('serialcodes/adminhtml_serialcodes_items'));
        $this->renderLayout();
    }

    public function editAction()
    {
		$itemsId = $this->getRequest()->getParam('id');
        $itemsModel  = Mage::getModel('sales/order_item')->load($itemsId);
 
        if($itemsModel->getId()) {
 
            Mage::register('serialcodes_items_data', $itemsModel);
 
            $this->loadLayout();
            $this->_setActiveMenu('sales/serialcodes_items');
           
            $this->_addBreadcrumb(Mage::helper('serialcodes')->__('Serial Code Order Items'), Mage::helper('serialcodes')->__('Serial Code Order Items'));
            $this->_addBreadcrumb(Mage::helper('serialcodes')->__('Edit Codes'), Mage::helper('serialcodes')->__('Edit Codes'));
           
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
           
            $this->_addContent($this->getLayout()->createBlock('serialcodes/adminhtml_serialcodes_items_edit'))
                 ->_addLeft($this->getLayout()->createBlock('serialcodes/adminhtml_serialcodes_items_edit_tabs'));
               
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('serialcodes')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

	public function saveAction()
	{
		if($this->getRequest()->getPost()) {
			try {
				$postData = $this->getRequest()->getPost();
				$codearray = explode("\n",$postData['serial_codes']);
				$codearray = array_map('trim', $codearray);
				$codearray = array_filter($codearray);
				$codes = implode("\n",$codearray);
				$pid = $this->getRequest()->getParam('id');
				Mage::getModel('sales/order_item')->load($pid)
					->setSerialCodeType(trim($postData['serial_code_type']))
					->setSerialCodes($codes)
					->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('serialcodes')->__('Successfully saved.'));
				Mage::getSingleton('adminhtml/session')->setSerialcodesData(false);
				$this->_redirect('*/*/');
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setSerialcodesData($this->getRequest()->getPost());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
				return;
			}
		}
		$this->_redirect('*/*/');
	}

    public function savepopupAction()
    {
		if($this->getRequest()->getPost()) {
			try {
				$pid = $this->getRequest()->getParam('id');
				$postData = $this->getRequest()->getPost();
				$codearray = explode("\n",$postData['serial_codes_'.$pid]);
				$codearray = array_map('trim', $codearray);
				$codearray = array_filter($codearray);
				$codes = implode("\n",$codearray);
				$item = Mage::getModel('sales/order_item')->load($pid);
				$orderid = $item->getOrderId();
				$item
					->setSerialCodeType(trim($postData['sc_type_'.$pid]))
					->setSerialCodes($codes)
					->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('serialcodes')->__('Successfully saved.'));
				Mage::getSingleton('adminhtml/session')->setSerialcodesData(false);
				$this->_redirect('adminhtml/sales_order/view', array('order_id' => $orderid));
				return;
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				Mage::getSingleton('adminhtml/session')->setSerialcodesData(false);
				$this->_redirect('adminhtml/sales_order/view', array('order_id' => $orderid));
				return;
			}
		}
		$this->_redirect('adminhtml/sales_order/view', array('order_id' => $orderid));
    }

	public function issueAction()
	{
		$pid = $this->getRequest()->getParam('id');
		$order = Mage::getSingleton('sales/order')->load($pid);
		$item = Mage::getSingleton('sales/order_item');
		$storeid = $order->getStoreId();
		$orderId = $order->getIncrementId();
		$itemids = explode(',',$this->getRequest()->getPost('sc_items'));
		foreach($itemids as $itemid)
		{
			$configured = 0;
			$saved = 0;
			$success = 0;
			$pcode = '';
			$item->load($itemid);
			$qty = round($item->getQtyOrdered());
			$product = Mage::getModel('catalog/product')->setStoreId($storeid)->load($item->getProductId());
			if(!$codetype = trim($product->getData('serial_code_type'))) {$codetype = 'Serial Code';}
			if(!$sku = trim($product->getData('serial_code_pool'))) {$sku = trim($product->getSku());}
			$codes = Mage::getSingleton('serialcodes/serialcodes')->getCollection()->addFieldToFilter('sku',array('like' => $sku))->load();
			for($i=1; $i<=$qty; $i++)
			{
				$num = 0;
				foreach($codes as $code)
				{
					$num++;
					$configured = 1;
					if($code->getStatus() == 0)
					{
						if($saved) {$pcode = $item->getSerialCodes()."\n";}
						$item->setSerialCodeType($codetype);
						$item->setSerialCodes($pcode.$code->getCode());
						$item->save();
						$code->setStatus(1);
						$code->setNote($orderId);
						$code->setUpdateTime(now());
						$code->save();
						$saved = 1;
						if(!$success) {Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('serialcodes')->__('Codes issued for %s.',$product->getName()));}
						$success = 1;
						break;
					} else {
						if ($num == count($codes))
						{
							if($saved) {$pcode = $item->getSerialCodes()."\n";}
							if(!trim($message = $product->getSerialCodeNotAvailable())) {$message = Mage::helper('serialcodes')->__('Oops! Not available.');}
							$item->setSerialCodeType($codetype);
							$item->setSerialCodes($pcode.$message);
							$item->save();
							$saved = 1;
							if($i == $qty) {Mage::getSingleton('adminhtml/session')->addError(Mage::helper('serialcodes')->__('Ran out of codes for %s.',$product->getName()));}
							break;
						}
					}
				}
				if(!$configured)
				{
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('serialcodes')->__('Unable to issue codes for %s. Check configuration.',$product->getName()));
					break;
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
						'codetype'	=> $codetype,
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
		$this->_redirect('adminhtml/sales_order/view', array('order_id' => $pid));
	}

	public function emailAction()
	{
		$templatearray = array();
		$pid = $this->getRequest()->getParam('id');
		$order = Mage::getSingleton('sales/order')->load($pid);
		$storeid = $order->getStoreId();
		$item = Mage::getSingleton('sales/order_item');
		$orderId = $order->getIncrementId();
		$itemids = explode(',',$this->getRequest()->getPost('sc_items'));
		foreach($itemids as $itemid)
		{
			$item->load($itemid);
			$product = Mage::getModel('catalog/product')->setStoreId($storeid)->load($item->getProductId());
			$codes = explode("\n",$item->getSerialCodes());
			if($codes[0])
			{
				$template = $product->getSerialCodeEmailTemplate();
				$templatearray[$template]['emailtype'] = $product->getSerialCodeEmailType();
				$templatearray[$template]['bcc'] = $product->getSerialCodeSendCopy();
				if($type = $item->getSerialCodeType()) {$templatearray[$template]['codetype'] = $type;} else {$templatearray[$template]['codetype'] = 'Serial Code';}
				if(empty($templatearray[$template]['html'])) {$templatearray[$template]['html'] = '<div class="sc_items">';}
				if ($templatearray[$template]['html'] != '<div class="sc_items">') {$templatearray[$template]['html'] .= '<br /><br />';}
				$templatearray[$template]['html'] .= '<span class="sc_product">'.$product->getName().'</span>';
				foreach($codes as $code)
				{
					$templatearray[$template]['html'] .= '<br /><span class="sc_type">'.$templatearray[$template]['codetype'].':</span> <span class="sc_code">'.$code.'</span>';
				}
			} else {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('serialcodes')->__("Email for %s not sent ('Serial Codes' field empty on order).",$product->getName()));
			}
		}
		foreach ($templatearray as $template => $value)
		{
			$templatearray[$template]['html'] .= '</div>';
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
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('serialcodes')->__('An email containing %s(s) and %s has been sent to %s.',$value['codetype'], $value['emailtype'], $order->getCustomerEmail()));
			} else {
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('serialcodes')->__('An email containing %s(s) has been sent to %s.',$value['codetype'], $order->getCustomerEmail()));
			}
		}
		$this->_redirect('adminhtml/sales_order/view', array('order_id' => $pid));
	}

	public function gridAction()
	{
		$this->loadLayout();
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('importedit/adminhtml_serialcodes_items_grid')->toHtml()
		);
	}
}
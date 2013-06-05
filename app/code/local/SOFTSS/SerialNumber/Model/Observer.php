<?php

/*
 * Copyright 2013 pcfritz.de Onlinestore GmbH – http://www.pcfritz.de
 * all rights reserved
 */

/* * Numbercodes observer
 *
 * @encoding    UTF-8
 * @package     SOFTSS
 * @subpackage  SOFTSS_Serialcodes
 * @author      Nikolas Koumarianos <n.entwickler@pcfritz.de>
 * 
 */

class SOFTSS_SerialNumber_Model_Observer extends Mage_Core_Model_Abstract {

    protected $_logFileName = 'serialnumbers.log';

    public function scheduledSerialNumberEmail($observer) {
        
        Mage::log("Cron for assign Serial Numbers Started", null, $this->_logFileName);

        $collection = Mage::getResourceModel('sales/order_collection')
                ->addFieldToSelect('*')
                ->addFieldToFilter('softss_has_serialcode', 1)
                ->addFieldToFilter('softss_serialcode_sent', 0)
                ->addFieldToFilter('state', Mage_Sales_Model_Order::STATE_COMPLETE)
                ->setOrder('created_at', 'desc');


        foreach ($collection as $order) {

            $storeid = $order->getStoreId();
            $orderId = $order->getIncrementId();
            $items = $order->getAllItems();
            $saved = 0;
            $aSerialNumbers = array();
            foreach ($items as $item) {

                $product = Mage::getModel('catalog/product')->setStoreId($storeid)->load($item->getProductId());
                if ($product->getSerialCodeSerialized()) {
                    if ($parent = $item->getParentItem()) {
                        if ($parent->getProductType() == 'configurable' && !Mage::getModel('catalog/product')->load($parent->getProductId())->getSerialCodeSerialized()) {
                            $item = $parent;
                        }
                    }
                    $qty = round($item->getQtyOrdered());
                    if ('' == $sku = trim($product->getSerialCodePool())) {
                        $sku = trim($product->getSku());
                    }
                    $codes = Mage::getSingleton('serialcodes/serialcodes')->getCollection()->addFieldToFilter('sku', array('like' => $sku))->load();

                    for ($i = 1; $i <= $qty; $i++) {
                        $saved = 0;
                        if ($product->getSerialCodeType()) {
                            $codetype = trim($product->getSerialCodeType());
                        }
                        foreach ($codes as $code) {
                            $item->setSerialCodeType($codetype);

                            if ($code->getStatus() == 0) {

                                $pcode = $item->getSerialCodes();
                                if ($pcode) {
                                    $pcode .= "\n";
                                }
                                $attributeSetName = Mage::getModel('eav/entity_attribute_set')->load($product->getAttributeSetId())->getAttributeSetName();
                                $aSerialNumbers[] = array('attribute_set' => $attributeSetName, 'serialnumber' => $code->getCode());
                                $item->setSerialCodes($pcode . $code->getCode());
                                $item->save();
                                $code->setStatus(1);
                                $code->setNote($orderId);
                                $code->setUpdateTime(now());
                                $code->save();
                                $saved = 1;
                                Mage::log("Serial Numbers assigned " . $pcode . $code->getCode(), null, $this->_logFileName);
                                break;
                            }
                        }
                        if (!$saved) {
                            $pcode = $item->getSerialCodes();
                            if ($pcode) {
                                $pcode .= "\n";
                            }
                            if (!trim($message = $product->getSerialCodeNotAvailable())) {
                                $message = Mage::helper('serialcodes')->__('Oops! Not available.');
                            }
                            $item->setSerialCodeType($codetype);
                            $item->setSerialCodes($pcode . $message);
                            $item->save();
                            Mage::log("Serial Numbers not found to be assign", null, $this->_logFileName);
                            $saved = 1;
                        }
                    }
                }
            }
            if (!empty($aSerialNumbers)) {

                Mage::log("Order with serial numbers found.Order Id: " . $order->getId(), null, $this->_logFileName);

                $pdfFilePath = Mage::getBaseDir('media') . DS . 'pdf' . DS;
                $serialNumberPath = Mage::getBaseDir('media') . DS . 'serial_numbers' . DS;
                $serials = 0;

                if (!file_exists($serialNumberPath)) {
                    mkdir($serialNumberPath, 0777, true);
                }

                $template = Mage::getStoreConfig('serialnumber_conf/serialnumber_email/serialnumber_email_template');
                if (is_numeric($template)) {
                    $eTemplate = Mage::getModel('core/email_template')->load($template);
                } else {
                    $eTemplate = Mage::getModel('core/email_template')->loadDefault($template);
                }

                foreach ($aSerialNumbers as $aSerialNumber) {
                    try {
                        if ($aSerialNumber['attribute_set'] == 'windows') {

                            $filePath = $serialNumberPath . $aSerialNumber['serialnumber'] . '.pdf';

                            $pdf = Zend_Pdf::load($pdfFilePath . 'windows.pdf');
                            $page = $pdf->pages[0];
                            $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
                            $page->setFont($font, 11);
                            $page->drawText($aSerialNumber['serialnumber'], 115, 110);
                            $pdf->save($filePath);
                            if (file_exists($filePath)) {
                                $eTemplate->getMail()->createAttachment(
                                        file_get_contents($filePath), 'application/pdf', Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, basename($filePath)
                                );
                            }
                            $serials = 1;
                            Mage::log("Serial Number created with Id: " . $aSerialNumber['serialnumber'], null, $this->_logFileName);
                        } elseif ($aSerialNumber['attribute_set'] == 'office') {

                            $filePath = $serialNumberPath . $aSerialNumber['serialnumber'] . '.pdf';

                            $pdf = Zend_Pdf::load($pdfFilePath . 'office.pdf');
                            $page = $pdf->pages[0];
                            $font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA);
                            $page->setFont($font, 11);
                            $page->drawText($aSerialNumber['serialnumber'], 180, 157);
                            $pdf->save($filePath);
                            if (file_exists($filePath)) {
                                $eTemplate->getMail()->createAttachment(
                                        file_get_contents($filePath), 'application/pdf', Zend_Mime::DISPOSITION_ATTACHMENT, Zend_Mime::ENCODING_BASE64, basename($filePath)
                                );
                            }
                            Mage::log("Serial Number created with Id: " . $aSerialNumber['serialnumber'], null, $this->_logFileName);
                            $serials = 1;
                        }
                    } catch (Exception $e) {
                        Mage::log("Error creating pdf for: " . $aSerialNumber['serialnumber'], null, $this->_logFileName);
                    }
                }

                if ($serials) {

                    try {

                        $eVars = array('name' => $order->getBillingAddress()->getName());

                        $eTemplate->setSenderName(Mage::getStoreConfig('serialnumber_conf/serialnumber_email/name'));
                        $eTemplate->setSenderEmail(Mage::getStoreConfig('serialnumber_conf/serialnumber_email/sender_email_identity'));


                        $eTemplate->send(
                                $order->getCustomerEmail(), $order->getBillingAddress()->getName(), $eVars);

                        $order->setData('softss_serialcode_sent', 1);
                        $order->save();

                        Mage::log("Email for order: " . $order->getId() . ' sent!', null, $this->_logFileName);
                    } catch (Exception $e) {
                        Mage::log("Error for order: " . $order->getId() . '. No email sent!', null, $this->_logFileName);
                    }
                } else {
                    Mage::log("Products belong to another attribute set", null, $this->_logFileName);
                }
            }
        }



        Mage::log("Cron Serial Number Finished", null, $this->_logFileName);
    }

}
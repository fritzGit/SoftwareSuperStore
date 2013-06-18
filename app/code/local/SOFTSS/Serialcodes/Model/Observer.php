<?php

/*
 * Copyright 2013 pcfritz.de Onlinestore GmbH – http://www.pcfritz.de
 * all rights reserved
 */

/**
 * Serialcodes observer
 *
 * @encoding    UTF-8
 * @package     SOFTSS
 * @subpackage  SOFTSS_Serialcodes
 * @author      Nikolas Koumarianos <n.entwickler@pcfritz.de>
 *
 */

class SOFTSS_Serialcodes_Model_Observer extends Mmsmods_Serialcodes_Model_Observer
{
    const XML_SOFTDISTIBUTION_ORDER_URL          = 'checkout/softdistribution/url_order';
    const XML_SOFTDISTIBUTION_RESELLERID         = 'checkout/softdistribution/resellerid';
    const XML_SOFTDISTIBUTION_PASSWORD           = 'checkout/softdistribution/password';
    const XML_SOFTDISTIBUTION_ORDER_CANCEL_URL   = 'checkout/softdistribution/url_order_cancel';
    const XML_SOFTDISTIBUTION_EXT_ORDER_REQUEST  = 'checkout/softdistribution/url_order_ext';

    protected $_logFileName = 'serialcodes.log';
    protected $_logFileNameSoftD = 'softdistribution.log';


    public function flagOrderSerialCodes(Varien_Event_Observer $observer)
    {

        $session = Mage::getSingleton('checkout/session');
        $order = Mage::getSingleton('sales/order')->load($session->getLastOrderId());
        $storeid = $order->getStoreId();

        $items = $order->getAllItems();

        foreach ($items as $item)
        {
                $product = Mage::getModel('catalog/product')->setStoreId($storeid)->load($item->getProductId());
                if ($product->getSerialCodeSerialized() || $product->getSoftssSupplierProductId())
                {
                     $order->setData('softss_has_serialcode', 1);
                     $order->save();
                     break;
                }
        }
    }


    public function scheduledSerialcodesEmail()
    {

        Mage::log("Cron for assigning Serial Numbers Started", null, $this->_logFileName);

        $collection = Mage::getResourceModel('sales/order_collection')
                ->addFieldToSelect('*')
                ->addFieldToFilter('softss_has_serialcode', 1)
                ->addFieldToFilter('softss_serialcode_sent', 0)
                ->addFieldToFilter('state', Mage_Sales_Model_Order::STATE_COMPLETE)
                ->setOrder('created_at', 'desc');

        if (count($collection) > 0) {

            foreach ($collection as $order) {
                $orderItemResponseDetails = array();

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
                    } elseif ($product->getSoftssSupplierProductId()) {

                        $date = new DateTime();
                        $custId = $order->getBillingAddress()->getCustomerId();
                        $incrementId =$order->getIncrementId();
                        $resellertransid = $date->getTimestamp().$orderId.$custId;

                        $resellerID = Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_RESELLERID);
                        $pass = md5(Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_PASSWORD));

                        $url = Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_ORDER_URL);
                        $url .= '?resellerid='.$resellerID;
                        $url .= '&pass='.$pass;
                        $url .= '&id='.$product->getSoftssSupplierProductId();
                        $url .= '&qty='.(int)$item->getQtyOrdered();
                        $url .= '&orderref='.$orderId;
                        $url .= '&custref='.$custId;
                        $url .= '&resellertransid='.$resellertransid;

                        Mage::log('url:'.$url, null, $this->_logFileNameSoftD);
                        $responseXML = $this->getXML($url);
                        $aOrderDetail = array();
                        Mage::log('response xml:'.$responseXML, null, $this->_logFileNameSoftD);

                        if(isset($responseXML)) {
                            #$orderDetailXML = simplexml_load_string($orderXML, null, LIBXML_NOCDATA);
                            $response = new SimpleXMLElement($responseXML);
                        } else {
                            Mage::log("No xml order detail for order: ".$order->getIncrementId(), null, $this->_logFileNameSoftD);
                            $this->sendError('No xml order detail response for order', "No xml order detail for order: ".$incrementId);
                        }

                        if (preg_match("/<error>/", $responseXML))
                        {
                            $sErrormsgTitle    = (string)$response->errormsg->title;
                            $sErrormsgText  = (string)$response->errormsg->text;
                            $content = "Request URL: $url<br/><br/>$sErrormsgTitle<br/><br/>$sErrormsgText";
                            //send error mail
                            $this->sendError('Softdistribution download link response ERROR', $content);
                        }else{
                            $sProductpid        = (string)$response->productpid;
                            $sDownloadlink      = (string)$response->downloadlink;
                            $sTransactionid     = (string)$response->transactionid;
                            $sResellertransid   = (string)$response->resellertransid;
                            $sCustomerref       = (string)$response->customerref;
                            $sOrderref          = (string)$response->orderref;
                            $sAdditionalinfo    = (string)$response->additionalinfo;
                            $aSerial = array();
                            foreach($response->serials->serial as $serial){
                                $aSerial[] = (string)$serial;
                            }

                            $orderItemResponseDetails[] = array('productname'   =>$product->getName(),
                                                                'productpid'    =>$sProductpid,
                                                                'downloadlink'  =>$sDownloadlink,
                                                                'transactionid' =>$sTransactionid,
                                                                'resellertransid'=>$sResellertransid,
                                                                'customerref'   =>$sCustomerref,
                                                                'orderref'      =>$sOrderref,
                                                                'additionalinfo'=>$sAdditionalinfo,
                                                                'serials'       =>$aSerial);

                            $oSoftDistributionCodes = Mage::getModel('softd/softd');
                            $oSoftDistributionCodes->setProductpid($sProductpid);
                            $oSoftDistributionCodes->setItemid($item->getId());
                            $oSoftDistributionCodes->setDownloadlink($sDownloadlink);
                            $oSoftDistributionCodes->setTransactionid($sTransactionid);
                            $oSoftDistributionCodes->setResellertransid($sResellertransid);
                            $oSoftDistributionCodes->setCustomerref($sCustomerref);
                            $oSoftDistributionCodes->setOrderref($sOrderref);
                            $oSoftDistributionCodes->setAdditionalinfo($sAdditionalinfo);
                            $oSoftDistributionCodes->setSerialnumber(implode($aSerial));
                            $oSoftDistributionCodes->save();

                            if($product->getSoftssExtendedorderRequired() == 1 ){
                                $billing_address_data = $order->getBillingAddress();
                                $firstname  = $billing_address_data['firstname'];
                                $lastname   = $billing_address_data['lastname'];
                                $street     =  $billing_address_data['street'];
                                $city       = $billing_address_data['city'];
                                $postcode   = $billing_address_data['postcode'];
                                $telephone  = $billing_address_data['telephone'];
                                $fax  = $billing_address_data['telephone'];
                                $country_id = $billing_address_data['country_id'];

                                $productID = $item->getProductId();
                                $qty = $item->getQtyOrdered();
                                $sXMLRequestExtended = "<order>
       <access>
             <resellerid>$resellerID</resellerid>
             <pass>$pass</pass>
       </access>
       <customer>
             <companyname></companyname>
             <title>Dr.</title>
             <firstname>$firstname</firstname>
             <lastname>$lastname</lastname>
             <email>customer@email.com</email>
             <address1>$street</address1>
             <address2></address2>
             <zipcode>$postcode</zipcode>
             <city>$city</city>
             <country_iso3166>$country_id</country_iso3166>
             <phone>$telephone</phone>
             <fax>$fax</fax>
             <language>EN</language>
       </customer>
       <product>
             <id>$productID</id>
             <qty>$qty</qty>
             <orderref>AB-1234</orderref>
             <custref>$custId</custref>
             <promotioncode></promotioncode>
             <backupcd>1</backupcd>
             <booking_comment></booking_comment>
             <resellertransid>$resellertransid</resellertransid>
       </product>
</order>";

                                $domdoc = new DOMDocument();
                                $domdoc->loadXML($sXMLRequestExtended);
                                $this->sendXMlRequest(Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_EXT_ORDER_REQUEST), $domdoc);
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
                                Mage::log("Serial Number Pdf created with Id: " . $aSerialNumber['serialnumber'], null, $this->_logFileName);
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
                                Mage::log("Serial Number Pdf created with Id: " . $aSerialNumber['serialnumber'], null, $this->_logFileName);
                                $serials = 1;
                            }
                        } catch (Exception $e) {
                            Mage::log("Error creating pdf for: " . $aSerialNumber['serialnumber'], null, $this->_logFileName);
                            $this->sendError('Softdistribution serial num. PDF creation ERROR', "Error creating pdf for: " . $aSerialNumber['serialnumber']);
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

                            Mage::log("Email for order: " . $order->getIncrementId() . ' sent! - '.now(), null, $this->_logFileName);
                        } catch (Exception $e) {
                            Mage::log("Error for order: " . $order->getIncrementId() . '. No email sent! - '.now().'--'.$e->getMessage(), null, $this->_logFileName);
                        }
                    } else {
                        Mage::log("Products belong to another attribute set", null, $this->_logFileName);
                    }
                }

                if(count($orderItemResponseDetails)>0){

                   try {
                        //send email to customer
                        $sCustomerFullName = $order->getBillingAddress()->getName();
                        $sCustomerEmail = $order->getCustomerEmail();

                        $emailTemplate = Mage::getModel('core/email_template')->loadDefault('serialnumber_conf_serialnumber_email_softdistserialnumber_email_template');
                        $emailTemplateVariables = array();
                        $emailTemplateVariables['name'] = $sCustomerFullName;

                        $sSerialDownloadEmailText = '';
                        foreach ($orderItemResponseDetails as $orderItemDetail) {
                            foreach ($orderItemDetail['serials'] as $serial) {
                                $sSerialDownloadEmailText .= '<td>' . $orderItemDetail['productname'] . '</td><td>' . $orderItemDetail['downloadlink'] . '</td><td>' . $serial . '</td>';
                            }
                        }

                        $emailTemplateVariables['download_serial'] = $sSerialDownloadEmailText;

                        $emailTemplate->getProcessedTemplate($emailTemplateVariables);
                        $salesName = Mage::getStoreConfig('serialnumber_conf/serialnumber_email/name');
                        $salesEmail = Mage::getStoreConfig('serialnumber_conf/serialnumber_email/sender_email_identity');

                        $emailTemplate->setSenderName($salesName);
                        $emailTemplate->setSenderEmail($salesEmail);
                        $emailTemplate->setTemplateSubject('Softwaresuperstore Download Link');
                        $emailTemplate->send($sCustomerEmail, $sCustomerFullName, $emailTemplateVariables, $emailTemplateVariables);

                        $order->setData('softss_serialcode_sent', 1);
                        $order->save();
                        
                        Mage::log("Email for order: " . $order->getIncrementId() . ' sent! - ' . now(), null, $this->_logFileName);
                    } catch (Exception $e) {
                        Mage::log("Error for order: " . $order->getIncrementId() . '. No email sent! - ' . now().'--'.$e->getMessage(), null, $this->_logFileName);
                    }
                }
            }
        } else {
            Mage::log("No orders found", null, $this->_logFileName);
        }


        Mage::log("Cron Serial Number Finished", null, $this->_logFileName);
    }

        /**
     * Simple function, which will get an xml file from softdestribution
     *
     * @param String url
     *
     * @return XMLDoc
     */
    protected function getXML($url)
    {
        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => 'cURL Request'
        ));

        // Send the request & save response to $resp
        $responce = curl_exec($curl);

        if(curl_errno($curl)){
            $curlError = curl_error($curl);
            Mage::log("Curl error: ".$curlError);
            $this->sendError('Softdistribution Curl error',$curlError);
        }

        // Close request to clear up some resources
        curl_close($curl);

        return $responce;
    }

    /**
    * Simple function, which will send a previously created XML to softdistribution and returns the
    * result as a SimpleXMLElement.
    *
    * @param DOMDocument $XMLData
    *
    * @return SimpleXMLElement
    */
    protected function sendXMlRequest( $url, DOMDocument $XMLData )
    {
        $curlHandle = curl_init( $url );
        curl_setopt( $curlHandle, CURLOPT_MUTE, 1 );
        curl_setopt( $curlHandle, CURLOPT_POST, 1 );
        curl_setopt( $curlHandle, CURLOPT_HTTPHEADER, array( 'Content-Type: text/xml' ) );
        curl_setopt( $curlHandle, CURLOPT_POSTFIELDS, $XMLData->saveXML() );
        curl_setopt( $curlHandle, CURLOPT_RETURNTRANSFER, 1 );

        $output = curl_exec( $curlHandle );
        if($output === false)
        {
            $curlError = curl_error($curlHandle);
            Mage::log('Curl error: ' . $curlError);
            $this->sendError('Softdistribution Extendedorder Curl error',$curlError);
        }

        $result = new SimpleXMLElement( $output );

        curl_close( $curlHandle );

        return $result;
    }

    protected function sendError($subject,$content){
        $mail = new Zend_Mail();
        $mail->setFrom("info@softwaresuperstore.co.uk","Softwaresuperstore Admin");
        $mail->addTo("j.galvez@pcfritz.de","Juan Galvez");
        $mail->setSubject($subject);
        $mail->setBodyHtml($content);

        try {
            $mail->send();
        }
        catch (Exception $e) {
            Mage::log('Mail not sent: '.$e->getMessage());
        }
    }

    public function sendCancelRequest(Varien_Event_Observer $observer)
    {

        $order = $observer->getEvent()->getOrder();

            $url = Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_ORDER_CANCEL_URL);
            $url .= '?resellerid='.Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_RESELLERID);
            $url .= '&pass='.md5(Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_PASSWORD));
            $url .= '&booking_comment=wrong_purchase';

            $items = $order->getAllItems();

            foreach ($items as $item) {

                try {

                    if($productpId = $item->getSoftssSupplierProductId()) {

                        $oSoftDistribution = Mage::getModel('softdistribution/softdistribution')->getCollection()
                                        ->addFieldToFilter('productpid', $productpId)
                                        ->addFieldToFilter('orderref', $order->getIncrementId())
                                        ->getFirstItem();

                        $url .= '&transacationid='.$oSoftDistribution->getTransactionid();

                        $orderXML = $this->getXML($url);

                        if(isset($orderXML)) {
                            $orderDetailXML = simplexml_load_string($orderXML, null, LIBXML_NOCDATA);
                        } else {
                            Mage::log("No xml order cancel response for order: ".$order->getIncrementId(), null, $this->_logFileNameSoftD);
                            return false;
                        }

                        $first = true;

                        foreach ($orderDetailXML as $orderData) {

                          //skip head node
                          if ($first) {
                              $first = false;
                              continue;
                          }

                          if($orderData->okmsg) {
                                Mage::log("Order cancelled with id: ".$order->getIncrementId(), null, $this->_logFileNameSoftD);
                          } else {
                                Mage::log("Order cancelation error for order: ".$order->getIncrementId(), null, $this->_logFileNameSoftD);
                          }
                        }
                  }

                } catch (Exception $e) {
                    Mage::log('Error canceling order: '.$order->getIncrementId().'for item '.$item->getName(). $e->getMessage(), null, $this->_logFileNameSoftD);
                }

            }
            return;

    }
}

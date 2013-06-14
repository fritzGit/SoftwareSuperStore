<?php

/**
 * Magento2Afterbuy
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to German Copyright Law (Urheberrecht)
 * The license file is bundled with this software
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@pimpmyxtc.de so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2009 Michael Kreutzer, pimpmyxtc.de
 */

/**
 *
 *
 * @author     Michael Kreutzer <info@pimpmyxtc.de>
 * @copyright  Copyright (c) 2009-2012 Michael Kreutzer, pimpmyxtc.de
 */
class Afterbuy_Afterbuycheckout_Model_Orderafterbuy extends Mage_Sales_Model_Order {

    public $afterbuy_string = "";
    public $order_id = "";
    public $checkstatus_order_id = "";
    public $mailadresse_error = "";
    public $payment_state = 0;
    public $checkout_activate = 1;
    public $handler = 'checkout'; //cron oder checkout
    public $history = "";
    public $zahlartenaufschlag = 1;
    public $showResult = 0;
    public $check_doppelbestellung = 1;
    public $logging_file = 0;
    protected $rawXml = "<?xml version=\"1.0\" encoding=\"utf-8\"?><Request></Request>";
    protected $apiURL = "https://api.afterbuy.de/afterbuy/ABInterface.aspx";

    public function getKundennr() {
        $sql = 'SELECT `shoporderid`, `kundennr` FROM `afterbuyorderdata` WHERE `shoporderid` = ' . $this->getData('increment_id');
        $read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $result = $read->fetchAll($sql);
        if (count($result) > 0) {
            $kdnr = $result[0]['kundennr'];
        } else {
            $kdnr = '';
        }

        return $kdnr;
    }

    public function place() {
        if ($this->checkout_activate == 1) {
            //Order-ID
            $this->order_id = $this->getRealOrderId();

            $this->createAfterbuyString();
            $this->send();
        }
        parent::place();
    }

    /**
     * Umwandlung Punkt-getrennte Preise in Komma-getrennte Preise
     *
     * @param mixed $wert
     * @return string
     */
    public function shop_preis_to_ab_preis($wert) {
        $wert2 = str_replace('.', ',', $wert);
        return $wert2;
    }

    /**
     * Decrypt data
     *
     * @param   string $data
     * @return  string
     */
    public function decrypt($data) {
        if ($data) {
            return Mage::helper('core')->decrypt($data);
        }
        return $data;
    }

    /**
     * Setter OrderID
     *
     * @param   string $orderid
     */
    public function setOrderID($orderid) {
        $this->order_id = $orderid;
    }

    protected function _prepareHistoryItem($label, $notified, $created, $comment = '') {
        return array(
            'title' => $label,
            'notified' => $notified,
            'comment' => $comment,
            'created_at' => $created
        );
    }

    public function createAfterbuyString() {
        $user = Mage::getModel('afterbuycheckout/afterbuycheckout')->load(1);
        if ($user->getData('status') != 1)
            return;

        $this->payment_state = 0;
        $order = Mage::getModel('sales/order')->loadByIncrementId($this->order_id);
        $_totalData = $order->getData();


        $carrierModel = $order->getData('shipping_carrier');
        //$cart = Mage::helper('checkout/cart');
        $shipping_adress = $order->getShippingAddress();
        if ($shipping_adress)
            $shipping = $shipping_adress->getData();
        $billing_adress = $order->getBillingAddress();
        $billing = $billing_adress->getData();
        $itemCollection = $order->getItemsCollection();

        $payment = $order->getPayment();
        $payment_data = $payment->getData();
        //jg change incase of underfined var
        if (isset($payment_data['additional_information']['paypal_payer_id']))
            $paypal_payer_id = $payment_data['additional_information']['paypal_payer_id'];
        else
            $paypal_payer_id = null;

        $transaction_id = $order->getPayment()->getLastTransId();
        $zahlart = $payment_data['method'];

        $this->history = array();
        foreach ($order->getAllStatusHistory() as $orderComment) {

            $this->history[] = $this->_prepareHistoryItem(
                    $orderComment->getStatusLabel(), $orderComment->getIsCustomerNotified(), $orderComment->getCreatedAtDate(), $orderComment->getComment()
            );
        }
        //echo $order->getState();
        //echo $order->getStatusLabel();
        //Paymorrow und Sofortüberweisung nur, wenn bereits bezahlt
        if (1 != 1)
        //if (($zahlart == 'paymorrow' && $order->getState() != 'processing') ||
        //	($zahlart == 'pnsofortueberweisung' && $order->getState() != 'complete') )

        /* ($zahlart == 'pnsofortueberweisung' && $order->getState() == 'canceled') ||
          ($zahlart == 'pnsofortueberweisung' && $order->getState() == 'pending_payment') ) */
            return;


        $note = $order->getEmailCustomerNote();
        $artikel_anz = 0;
        foreach ($itemCollection as $item) {
            $artikel_anz++;
            $artikel[] = $item->getData();
            $artikel_options[] = $item->getProductOptions();
        }
        $this->check_doppelbestellung = $user->getData('check_doppelbestellung');
        $this->mailadresse_error = $user->getData('shopbetreiber_mailadresse');
        $ab_data =
                "Action=new" .
                "&PartnerID=" . $user->getData('partner_id') .
                "&PartnerPass=" . $user->getData('partner_pass') .
                "&UserID=" . $user->getData('user_name');

        if (($billing['firstname'] == "") ||
                ($billing['lastname'] == "") ||
                ($billing['street'] == "") ||
                ($billing['postcode'] == "") ||
                ($billing['city'] == "" )) {
            if ($shipping) {
                $ab_data.=
                        "&KFirma=" . urlencode(utf8_decode($shipping['company']))
                        . "&KAnrede=" . urlencode(utf8_decode($shipping['prefix']))
                        . "&KVorname=" . urlencode(utf8_decode(($shipping['firstname'] . " " . $shipping['middlename'])))
                        . "&KNachname=" . urlencode(utf8_decode($shipping['lastname']))
                        . "&KStrasse=" . urlencode(utf8_decode($shipping['street']))
                        . "&KPLZ=" . urlencode($shipping['postcode'])
                        . "&KOrt=" . urlencode(utf8_decode($shipping['city']))
                        . "&KLand=" . urlencode($shipping['country_id']);
            }
        } else {
            $ab_data.=
                    "&KFirma=" . urlencode(utf8_decode($billing['company']))
                    . "&KAnrede=" . urlencode(utf8_decode($billing['prefix']))
                    . "&KVorname=" . urlencode(utf8_decode(($billing['firstname'] . " " . $billing['middlename'])))
                    . "&KNachname=" . urlencode(utf8_decode($billing['lastname']))
                    . "&KStrasse=" . urlencode(utf8_decode($billing['street']))
                    . "&KPLZ=" . urlencode($billing['postcode'])
                    . "&KOrt=" . urlencode(utf8_decode($billing['city']))
                    . "&KTelefon=" . urlencode($billing['telephone'])
                    . "&KLand=" . urlencode($billing['country_id']);
        }
        $ab_data.="&Kemail=" . urlencode($_totalData['customer_email']);

        /* if( ($billing['company']    	== $shipping['company']) &&
          ($billing['firstname'] 	 	== $shipping['firstname']) &&
          ($billing['lastname'] 		== $shipping['lastname']) &&
          ($billing['street']  		== $shipping['street']) &&
          ($billing['postcode']      	== $shipping['postcode']) &&
          ($billing['city']      		== $shipping['city'] )) */
        if (true) {
            $ab_data .= "&Lieferanschrift=0";
        } else {
            $ab_data .= "&Lieferanschrift=1";
            if ($shipping['lastname'] == "") {
                $array_shipping_name = array_reverse(explode(" ", $shipping['firstname']));
                $shipping['lastname'] = $array_shipping_name[0];
                $shipping['firstname'] = str_replace(" " . $shipping['lastname'], "", $shipping['firstname']);
            }
            $ab_data.=
                    "&KLFirma=" . urlencode(utf8_decode($shipping['company']))
                    . "&KLVorname=" . urlencode(utf8_decode(($shipping['firstname'] . " " . $shipping['middlename'])))
                    . "&KLNachname=" . urlencode(utf8_decode($shipping['lastname']))
                    . "&KLStrasse=" . urlencode(utf8_decode($shipping['street']))
                    . "&KLPLZ=" . urlencode($shipping['postcode'])
                    . "&KLOrt=" . urlencode(utf8_decode($shipping['city']))
                    . "&KLTelefon=" . urlencode($shipping['telephone'])
                    . "&KLLand=" . urlencode(utf8_decode($shipping['country_id']));
        }


        $ab_data .= "&Artikelerkennung=" . $user->getData('artikelerkennung');
        $ab_data .= "&Kundenerkennung=" . $user->getData('kundenerkennung');
        $ab_data .= "&NoFeedback=" . $user->getData('feedback');
        $ab_data .= "&NoVersandCalc=" . $user->getData('versand');



        $ab_data .= "&VID=" . $this->order_id;
        $benutzername = $this->order_id;

        /* $read = Mage::getSingleton('core/resource')->getConnection('core_read');
          $sql  = "select * from  sales_flat_quote_payment ";
          $test = $read->fetchAssoc($sql); */
        //$test = $read->query($sql);
        $ab_data.="&Kbenutzername=" . urlencode($benutzername);

        //$benutzername = $_totalData['customer_email'];


        $i = 0;
        $configurable_flag = 0;
        $temp_art_preis = 0;
        $temp_art_steuer = 0;

        foreach ($artikel as $artikel_key => $artikel_single) {

            //if ($artikel_single['quote_parent_item_id'] == "" )
            if (!array_key_exists('quote_parent_item_id', $artikel_single))
                $artikel_single['quote_parent_item_id'] = "";
            if ($artikel_single['quote_parent_item_id'] == "" AND $artikel_single['product_type'] != 'configurable') {
                $i++;

                $artnr = preg_replace('/[^0-9]*/', '', $artikel_single['sku']);
                $product = Mage::getModel('catalog/product');
                ;
                $productId = $product->getIdBySku($artikel_single['sku']);
                $product_c = $product->load($productId);
                //echo $product_c->getProductUrl();
                $ab_data .= "&ArtikelLink_" . ($i) . "=" . $product_c->getProductUrl();

                if ($artnr == "")
                    $artnr = "11111111";
                $ab_data .= "&Artikelnr_" . ($i) . "=" . urlencode($artnr);
                //$ab_data .= "&AlternArtikelNr1_".($i)."=".urlencode($transaction_id);

                $ab_data .= "&ArtikelStammID_" . ($i) . "=" . urlencode($artikel_single['sku']);
                $ab_data .= "&ArtikelGewicht_" . ($i) . "=" . urlencode($this->shop_preis_to_ab_preis($artikel_single['row_weight']));
                $ab_data.= "&Artikelname_" . ($i) . "=" . urlencode((utf8_decode($artikel_single['name'])))
                        . "&ArtikelMenge_" . ($i) . "=" . urlencode($this->shop_preis_to_ab_preis($artikel_single['qty_ordered']));
                if ($artikel_single['product_type'] == 'configurable' OR $artikel_single['product_type'] == 'super') {
                    $attribut_string = "";
                    $configurable_flag = 1;
                    $artikel_options_key = $artikel_options[$artikel_key];

                    if (array_key_exists('attributes_info', $artikel_options_key)) {
                        foreach ($artikel_options_key['attributes_info'] as $attribut_single) {
                            if ($attribut_string != "")
                                $attribut_string .= "|";
                            $attribut_string .=
                                    urlencode(utf8_decode($attribut_single['label'])) . ":" .
                                    urlencode(utf8_decode($attribut_single['value']));
                        }
                        if ($attribut_string != "")
                            $ab_data .= "&Attribute_" . ($i) . "=" . $attribut_string;
                    }
                }
                if ($artikel_single['product_type'] == 'simple' || $artikel_single['product_type'] == 'grouped' || $artikel_single['product_type'] == 'downloadable') {
                    $attribut_string = "";
                    if (is_array($artikel_single['product_options']) && isset($artikel_single['product_options'])) {
                        $unserialized_options = unserialize($artikel_single['product_options']);
                        unset($unserialized_options['info_buyRequest']);
                        if (!array_key_exists('options', $unserialized_options))
                            $artikel_options_key['options'] = $unserialized_options['options'];
                        else
                            $artikel_options_key['options'] = "";
                    }
                    else
                        $artikel_options_key = $artikel_options[$artikel_key];
                    if (is_array($artikel_options_key) && array_key_exists('options', $artikel_options_key)) {
                        if (count($artikel_options_key['options']) > 0) {

                            foreach ($artikel_options_key['options'] as $attribut_single) {
                                if ($attribut_string != "")
                                    $attribut_string .= "|";
                                $attribut_string .=
                                        urlencode(utf8_decode($attribut_single['label'])) . ":" .
                                        urlencode(utf8_decode($attribut_single['value']));
                            }
                        }
                    }
                    if ($temp_art_preis == 0) {
                        if ($artikel_single['price_incl_tax'] != "")
                            $temp_art_preis = $this->shop_preis_to_ab_preis((float) $artikel_single['price_incl_tax']);
                        else
                            $temp_art_preis = $this->shop_preis_to_ab_preis(((float) $artikel_single['row_total'] + (float) $artikel_single['tax_amount']) / $artikel_single['qty_ordered']);

                        $temp_art_steuer = $this->shop_preis_to_ab_preis($artikel_single['tax_percent']);
                    }

                    $ab_data .= "&ArtikelEpreis_" . ($i) . "=" . urlencode($temp_art_preis)
                            . "&ArtikelMwSt_" . ($i) . "=" . urlencode($temp_art_steuer);

                    $temp_art_preis = (float) 0;
                    $temp_art_steuer = (float) 0;
                }
                //jg change to avoid Undefined variable
                if (isset($attribut_string_configurable)) {
                    $ab_data .= "&Attribute_" . ($i) . "=" . $attribut_string_configurable;
                    $attribut_string_configurable = "";
                } else {
                    $ab_data .= "&Attribute_" . ($i) . "=" . $attribut_string;
                    $attribut_string = "";
                }
            } else {
                $temp_art_preis = (float) 0;
                $temp_art_steuer = (float) 0;
                if ($artikel_single['product_type'] == 'configurable') {
                    $attribut_string_configurable = "";
                    $configurable_flag = 1;
                    $artikel_options_key = $artikel_options[$artikel_key];

                    if (array_key_exists('attributes_info', $artikel_options_key)) {
                        foreach ($artikel_options_key['attributes_info'] as $attribut_single) {
                            if ($attribut_string_configurable != "")
                                $attribut_string_configurable .= "|";
                            $attribut_string_configurable .=
                                    urlencode(utf8_decode($attribut_single['label'])) . ":" .
                                    urlencode(utf8_decode($attribut_single['value']));
                        }
                    }
                    $temp_art_preis = $this->shop_preis_to_ab_preis(((float) $artikel_single['row_total'] + (float) $artikel_single['tax_amount']) / $artikel_single['qty_ordered']);
                    $temp_art_steuer = $this->shop_preis_to_ab_preis($artikel_single['tax_percent']);
                }
            }
        }
        // jg change Undefined index
        if (isset($_totalData['cod_fee'])) {
            if ((float) $_totalData['cod_fee'] != 0) {
                if ($this->zahlartenaufschlag == 1)
                    $ab_data .= "&ZahlartenAufschlag=" . urlencode($this->shop_preis_to_ab_preis((float) $_totalData['cod_fee']));

                else {
                    $i++;
                    $ab_data .= "&Artikelnr_" . ($i) . "=999998";
                    $ab_data .= "&Artikelname_" . ($i) . "=Nachnahmegebuehr";
                    $ab_data .= "&ArtikelEpreis_" . ($i) . "=" . urlencode($this->shop_preis_to_ab_preis((float) $_totalData['cod_fee']));
                    $ab_data .= "&ArtikelMwSt_" . ($i) . "=" . urlencode($this->shop_preis_to_ab_preis($artikel_single['tax_percent']));
                    $ab_data .= "&ArtikelMenge_" . ($i) . "=1";
                }
            }
        }
        if (isset($_totalData['discount_amount'])) {
            if ((float) $_totalData['discount_amount'] != 0) {
                if ($this->zahlartenaufschlag == 1)
                    $ab_data .= "&ZahlartenAufschlag=" . urlencode($this->shop_preis_to_ab_preis((float) $_totalData['discount_amount']));

                else {
                    $i++;
                    $ab_data .= "&Artikelnr_" . ($i) . "=999999";
                    $ab_data .= "&Artikelname_" . ($i) . "=Rabatt";
                    $ab_data .= "&ArtikelEpreis_" . ($i) . "=" . urlencode($this->shop_preis_to_ab_preis((float) $_totalData['discount_amount']));
                    $ab_data .= "&ArtikelMwSt_" . ($i) . "=" . urlencode($this->shop_preis_to_ab_preis($artikel_single['tax_percent']));
                    $ab_data .= "&ArtikelMenge_" . ($i) . "=1";
                }
            }
        }

//		$ab_data.="&PosAnz=".$artikel_anz;
        $ab_data.="&PosAnz=" . $i;

        $zahlart = $payment_data['method'];
        if ($zahlart == 'paypal_standard') {
            $zahlart_text = 'PayPal';
            if ($order->getStatusLabel() == 'Verarbeitung')
                $this->payment_state = 1;
        }
        elseif ($zahlart == 'paypal_express') {
            $zahlart_text = 'PayPalExpress';
            if ($order->getStatusLabel() == 'Verarbeitung')
                $this->payment_state = 1;
        }
        elseif (substr($zahlart, 0, 6) == 'paypal') {
            $zahlart_text = 'PayPal';
            if ($order->getStatusLabel() == 'Verarbeitung')
                $this->payment_state = 1;
        }
        elseif ($zahlart == 'debit') {
            $zahlart_text = 'Bankeinzug';
            if ($payment_data['cc_type'] != "") {
                $ab_data .= "&BLZ=" . $this->decrypt($payment_data['cc_type']);
            }
            if ($payment_data['cc_number_enc'] != "") {
                $ab_data .= "&Kontonummer=" . $this->decrypt($payment_data['cc_number_enc']);
            }
            //$ab_data .= "&Bankname=".ereg_replace(" ","%20",$b_data['banktransfer_bankname'])."&";

            if ($payment_data['cc_owner'] != "") {
                $ab_data .= "&Kontoinhaber=" . urlencode(utf8_decode($payment_data['cc_owner']));
            }
        } elseif ($zahlart == 'paymorrow') {
            $zahlart_text = 'Paymorrow';
            if ($payment->getPaymorrowBankCode($request['nationalBankCode']) != "") {
                $ab_data .= "&BLZ=" . $payment->getPaymorrowBankCode($request['nationalBankCode']);
                $ab_data .= "&ZahlartFID=99";
            }
            if ($payment->getPaymorrowAccountNumber($request['nationalBankAccountNumber']) != "") {
                $ab_data .= "&Kontonummer=" . $payment->getPaymorrowAccountNumber($request['nationalBankAccountNumber']);
            }
            //$ab_data .= "&Bankname=".ereg_replace(" ","%20",$b_data['banktransfer_bankname'])."&";

            /* if($payment_data['cc_owner'] != "")
              {
              $ab_data .= "&Kontoinhaber=".urlencode(utf8_decode($payment_data['cc_owner']));
              } */
        } elseif ($zahlart == 'pickup')
            $zahlart_text = 'Barzahlung';
        elseif ($zahlart == 'checkmo')
            $zahlart_text = 'Ueberweisung';
        elseif ($zahlart == 'bankpayment')
            $zahlart_text = 'Vorkasse / Überweisung';
        elseif ($zahlart == 'heidelpay_cc')
            $zahlart_text = 'Kreditkarte';
        elseif ($zahlart == 'quent_cc')
            $zahlart_text = 'Kreditkarte';
        elseif ($zahlart == 'pnsofortueberweisung') {
            $zahlart_text = 'Sofortüberweisung';
            if ($order->getStatusLabel() == 'Neu')
                $this->payment_state = 1;
        }
        elseif ($zahlart == 'cashondelivery')
            $zahlart_text = 'Nachnahme Deutschland';
        elseif ($zahlart == 'billsafe') {
            $zahlart_text = 'Rechnungskauf (BillSAFE)';
            $this->payment_state = 1;
        } elseif ($zahlart == 'moneybookers_acc')
            $zahlart_text = 'Kreditkarte';
        elseif ($zahlart == 'hpcc')
            $zahlart_text = 'Kreditkarte';
        elseif ($zahlart == 'ig_cashondelivery') {
            $zahlart_text = 'Versand per Nachnahme';
            $zahl_id = "4";
        }
        else
            $zahlart_text = $zahlart;

        //echo $payment->getMethodInstance()->getCode();// != 'cashondelivery'
        /* COD
          $cod = $order->getCodFee();
          $ab_data.= "&ZahlartenAufschlag=".$this->shop_preis_to_ab_preis((float)$cod);
         */
        $ab_data.= "&Zahlart=" . urlencode($zahlart_text);
        $versandart = $_totalData['shipping_method'];
        $versandart2 = $_totalData['shipping_description'];

        if ($versandart2 != "")
            $versandart_text = urlencode($versandart2);
        else {
            if ($versandart == 'flatrate_flatrate')
                $versandart_text = 'Festpreis';
            elseif ($versandart == 'freeshipping_freeshipping')
                $versandart_text = 'versandkostenfrei';
            elseif (substr($versandart, 0, 10) == 'matrixrate')
                $versandart_text = 'Versandmatrix';

            else
                $versandart_text = $versandart;
        }
        $ab_data.= "&Versandart=" . urlencode($versandart_text);
        if ($this->check_doppelbestellung == 1)
            $ab_data.= "&CheckVID=1";

        //SSS colour mark for afterbuy
        $ab_data.= "&MarkierungID=1000002887";

        /*         * * REMOVE ** */
        //$this->payment_state = 1;
        /*         * * REMOVE ** */
        if ($this->payment_state == 1)
            $ab_data.= "&SetPay=1";

        if ($_totalData['shipping_incl_tax'] != "")
            $shipping_complete = (float) $_totalData['shipping_incl_tax'];
        else
            $shipping_complete = ((float) $_totalData['shipping_amount'] + (float) $_totalData['shipping_tax_amount']);
        $ab_data.= "&Versandkosten=" . urlencode(str_replace('.', ',', $shipping_complete));
        //$ab_data.= "&Versandkosten=".urlencode(str_replace('.', ',', $_totalData['shipping_amount']));
        //falls vorhanden:
        //$kommentar = $this->getBiebersdorfCustomerordercomment();
        //$ab_data.= "&Kommentar=".urlencode($kommentar);

        /* $kommentar = $order->getData('customer_note');
          $ab_data.= "&Kommentar=".urlencode($kommentar); */
        // echo "<pre>";
        //$ab_data.= "&VMemo=".urlencode($transaction_id);
        if (is_array($this->history) && count($this->history) > 0) {
            $last_comment = array_shift($this->history);
            $ab_data.= "&Kommentar=" . urlencode($last_comment['comment']);
        }

        $ab_data.= '&VorgangsInfo3=Softwaresuperstore';

        $this->afterbuy_string = $ab_data;
    }

    protected function getPartnerID() {
        if ($this->partner_id == null) {
            $user = Mage::getModel('afterbuycheckout/afterbuycheckout')->load(1);
            $this->partner_id = $user->getData('partner_id');
        }
        return $this->partner_id;
    }

    protected function getPartnerPass() {
        if ($this->partner_pass == null) {
            $user = Mage::getModel('afterbuycheckout/afterbuycheckout')->load(1);
            $this->partner_pass = $user->getData('partner_pass');
        }
        return $this->partner_pass;
    }

    protected function getUserID() {
        if ($this->user_id == null) {
            $user = Mage::getModel('afterbuycheckout/afterbuycheckout')->load(1);
            $this->user_id = $user->getData('user_name');
        }
        return $this->user_id;
    }

    protected function getUserPassword() {
        if ($this->user_password == null) {
            $user = Mage::getModel('afterbuycheckout/afterbuycheckout')->load(1);
            $this->user_password = $user->getData('user_name');
        }
        return $this->user_password;
    }

    public function send() {

        if ($this->afterbuy_string != "") {
            $afterbuy_URL = 'https://api.afterbuy.de/afterbuy/ShopInterface.aspx';

            // connect
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "$afterbuy_URL");

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->afterbuy_string);
            $result = curl_exec($ch);
            curl_close($ch);


            $mail_content = '&Uuml;bertragung der Bestellung an Afterbuy: ' . chr(13) . chr(10) . $result . chr(13) . chr(10) . $this->afterbuy_string . chr(13) . chr(10);
            Mage::log(__LINE__ . ' | ' . __METHOD__ . "Daten: " . $afterbuy_URL . $this->afterbuy_string);
            if ($this->showResult == 1)
                print_r($result);
            $xml = new SimpleXMLElement($result);
            /*             * ************************************** */
            if (preg_match("/<success>1<\/success>/", $result)) {
                $tableName = Mage::getSingleton('core/resource')->getTableName('afterbuyorderdata');

                $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                $query = "insert into " . $tableName . " (shoporderid, success, errorcode, bezahlt, kundennr, aid, uid, update_time)
					values (:shoporderid, :success, :errorcode, :bezahlt, :kundennr, :aid, :uid, NOW())";

                $binds = array(
                    'shoporderid' => $this->order_id,
                    'success' => $xml->success,
                    'errorcode' => 'ok',
                    'bezahlt' => $this->payment_state,
                    'kundennr' => $xml->data->KundenNr,
                    'aid' => $xml->data->AID,
                    'uid' => $xml->data->UID
                );

                $write->query($query, $binds);
            } else {
                $tableName = Mage::getSingleton('core/resource')->getTableName('afterbuyorderdata');

                $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                $query = "insert into " . $tableName . " (shoporderid, success, errorcode, kundennr, aid, uid, update_time)
					values (:shoporderid, :success, :errorcode, :kundennr, :aid, :uid, NOW())";
                $binds = array(
                    'shoporderid' => $this->order_id,
                    'success' => '0',
                    'errorcode' => $xml->errorlist->error,
                    'kundennr' => '',
                    'aid' => '',
                    'uid' => ''
                );

                $write->query($query, $binds);
                $mail_content = 'Fehler bei &Uuml;bertragung der Bestellung an Afterbuy: ' . chr(13) . chr(10) .
                        'Folgende Fehlermeldung wurde gemeldet:' . chr(13) . chr(10) . $result . chr(13) . chr(10);
                //mail("", "Afterbuy-Fehl&uuml;bertragung", $mail_content);
                Mage::log("Afterbuy-Fehl&uuml;bertragung" . $mail_content);

                if ($this->logging_file == 1)
                    file_put_contents('/app/code/local/Afterbuy/Afterbuycheckout/Model/afterbuylog.txt', $this->afterbuy_string . "\r\n" . $result);
                //$transport = Mage::helper('smtppro')->getTransport();
                $mail = new Zend_Mail();

                $mail->setBodyText($mail_content);
                $mail->setFrom($this->mailadresse_error, 'Afterbuy');
                $mail->addTo($this->mailadresse_error, 'Admin');
                $mail->setSubject('Afterbuy Fehler');
                $mail->send();
                //$mail->send($transport);
            }
        }
    }

    public function updateMagentoOrderStatus($afterbuyID, $checkstatus_order_id) {

        $responseXML = $this->getAfterbuySoldItems($afterbuyID);

        $mail_content = 'Afterbuy status check: ' . chr(13) . chr(10) . $responseXML . chr(13) . chr(10) . ' Afterbuy orderID:' . $afterbuyID . chr(13) . chr(10);

        $xml = new SimpleXMLElement($responseXML);
        /*         * ************************************** */
        if (preg_match("/<CallStatus>Success<\/CallStatus>/", $responseXML)) {
            $abPaymentDate = (string) $xml->PaymentInfo->PaymentDate;
            $abAlreadyPaid = (string) $xml->PaymentInfo->AlreadyPaid;
            $abFullAmount = (string) $xml->PaymentInfo->FullAmount;

            if (isset($abPaymentDate) && $abAlreadyPaid == $abFullAmount)
                $status = 2; // compleate
            else
                $status = 1; // in process


            if ($status == 2) {
                //get Order
                $order = Mage::getModel('sales/order')->loadByIncrementID($checkstatus_order_id);
                $order->setStatus("complete");

                /**
                 * change order status to 'Complete' by creating the invoice
                 */
                try {

                    if (!$order->canInvoice()) {
                        #Mage::throwException(Mage::helper('core')->__('Cannot create an invoice. OrderId:'.$checkstatus_order_id));
                        Mage::log(Mage::helper('core')->__('Cannot create an invoice. OrderId:' . $checkstatus_order_id));
                    }

                    $savedQtys = array();
                    $invoice = Mage::getModel('sales/service_order', $order)->prepareinvoice($savedQtys);
                    if (!$invoice->getTotalQty()) {
                        #Mage::throwException(Mage::helper('core')->__('Cannot create an invoice without products. OrderID:'.$checkstatus_order_id));

                        Mage::log(Mage::helper('core')->__('Cannot create an invoice without products. OrderID:' . $checkstatus_order_id));
                    }
                    $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_invoice::CAPTURE_OFFLINE);
                    $invoice->register();

                    $invoice->getOrder()->setCustomerNoteNotify(false);
                    $invoice->getOrder()->setIsInProcess(true);

                    $transactionSave = Mage::getModel('core/resource_transaction')
                            ->addObject($invoice)
                            ->addObject($invoice->getOrder());

                    $transactionSave->save();

                    //update afterbuyorderdata table
                    $tableName = Mage::getSingleton('core/resource')->getTableName('afterbuyorderdata');
                    $write = Mage::getSingleton("core/resource")->getConnection("core_write");
                    $query = "update " . $tableName . " set bezahlt = :bezahlt, update_time = NOW() WHERE shoporderid = :shoporderid";
                    $binds = array(
                        'shoporderid' => $checkstatus_order_id,
                        'bezahlt' => $status
                    );
                    $write->query($query, $binds);
                } catch (Mage_Core_Exception $e) {
                    die($e->getMessage());
                }
            } elseif ($status == 1) {
                //get Order
                $order = Mage::getModel('sales/order')->load($checkstatus_order_id);
                $order->setStatus('processing');
                $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true)->save();
            }

            return $xml;
        } else {
            // Update afterbuyordertable
            $tableName = Mage::getSingleton('core/resource')->getTableName('afterbuyorderdata');
            $write = Mage::getSingleton("core/resource")->getConnection("core_write");
            $afterbuyError = $xml->errorlist->error;
            $query = "UPDATE $tableName SET success=0, errorcode=:errorcode, update_time=NOW() WHERE shoporderid=:shoporderid";
            $binds = array(
                'shoporderid' => $checkstatus_order_id,
                'errorcode' => $afterbuyError
            );
            $write->query($query, $binds);

            //Send error email.
            $mail_content = 'Magento OrderID : ' . $checkstatus_order_id . ', AfterBuy OrderId : ' . $afterbuyID . '. Fehler bei &Uuml;bertragung der Bestellung an Afterbuy: ' . chr(13) . chr(10) .
                    'Folgende Fehlermeldung wurde gemeldet:' . chr(13) . chr(10) . $responseXML . chr(13) . chr(10);

            if ($this->logging_file == 1)
                file_put_contents('/app/code/local/Afterbuy/Afterbuycheckout/Model/afterbuylog.txt', $responseXML);

            try {
                $mail = new Zend_Mail();
                $mail->setBodyText($mail_content);
                $mail->setFrom('j.galvez@pcfritz.de', 'Magento');
                $mail->addTo('j.galvez@pcfritz.de', 'Admin');
                $mail->setSubject('Afterbuy Fehler');
                $mail->send();
            } catch (exception $e) {
                Mage::log($e->getMessage());
            }

            return false;
        }
    }

    /* set afterbuy orders to compleate if compleate in Magento
     * return xml formatted string
     */
    public function setOrderPaymentStatusComplete() {
        $orders = Mage::getmodel('sales/order')->getCollection()
                ->addFieldToFilter('status', 'complete');

        foreach ($orders as $order) {
            $amount = $order->getGrandTotal();
            $orderIncrementId = $order->getIncrementId();
            $afterbuyOrderID = Mage::getModel('afterbuycheckout/afterbuycheckout')->loadByShoporderid($orderIncrementId);
            $responseXML = $this->createAfterbuyStatusXML($afterbuyOrderID, $amount);

            // if error...
            if (!preg_match("/<CallStatus>Success<\/CallStatus>/", $responseXML)) {
                try {
                    $mail_content = 'Magento Order Increment ID : ' . $orderIncrementId . ', AfterBuy OrderId : ' . $afterbuyOrderID . '. Fehler bei &Uuml;bertragung der Bestellung an Afterbuy: ' . chr(13) . chr(10) .
                    'Folgende Fehlermeldung wurde gemeldet:' . chr(13) . chr(10) . $responseXML . chr(13) . chr(10);

                    $mail = new Zend_Mail();
                    $mail->setBodyText($mail_content);
                    $mail->setFrom('j.galvez@pcfritz.de', 'Magento');
                    $mail->addTo('j.galvez@pcfritz.de', 'Admin');
                    $mail->setSubject('Afterbuy setOrderPaymentStatusComplete Fehler');
                    $mail->send();
                } catch (exception $e) {
                    Mage::log($e->getMessage());
                }
            }
        }
    }

    protected function callAPI($requestXML) {
        // connect
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "$this->apiURL");

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestXML);

        $result = curl_exec($ch);

        if ($result === false) {
            Mage::log(__LINE__ . ' | ' . __METHOD__ . 'URL: ' . $this->apiURL . 'Daten: ' . $requestXML);
            Mage::log(curl_error($ch));
        }

        curl_close($ch);

        return $result;
    }

    protected function setAfterbuyStatus($afterbuyOrderID, $amount) {

        $requestXml = $this->createAPIGlobals('UpdateSoldItems', '0');
        $requestXml->addChild("Orders");
        $requestXml->Orders->addChild("Order");
        $requestXml->Orders->Order->addChild("OrderID", $afterbuyOrderID);
        $requestXml->Orders->Order->addChild("PaymentInfo");
        $requestXml->Orders->Order->PaymentInfo->addChild("AlreadyPaid", str_replace(".", ",", $amount));
        $requestXml->Orders->Order->PaymentInfo->addChild("PaymentDate", date("d.m.Y", time()));
        $response = $this->callAPI($requestXml->asXml());
        return $response;
    }

    public function getAfterbuySoldItems($afterbuyorderid) {

        $requestXml = $this->createAPIGlobals('GetSoldItems', '2'); //2=payment information

        $requestXml->addChild("DataFilter");
        $requestXml->DataFilter->addChild("Filter");
        $requestXml->DataFilter->Filter->addChild("FilterName", 'OrderID');
        $requestXml->DataFilter->Filter->FilterName->addChild("FilterValues");
        $requestXml->DataFilter->Filter->FilterName->FilterValues->addChild("FilterValue", $afterbuyorderid);
        $response = $this->callAPI($requestXml->asXml());
        return $response;
    }

    public function createAPIGlobals($call, $detail = '0', $language = 'EN') {

        $requestXml = new SimpleXMLElement($this->rawXml);
        $requestXml->addChild("AfterbuyGlobal");
        $requestXml->AfterbuyGlobal->addChild("PartnerID", $this->getPartnerID());
        $requestXml->AfterbuyGlobal->addChild("PartnerPassword", $this->getPartnerPass());
        $requestXml->AfterbuyGlobal->addChild("UserID", $this->getUserID());
        $requestXml->AfterbuyGlobal->addChild("UserPassword", $this->getUserPassword());
        $requestXml->AfterbuyGlobal->addChild("CallName", $call);
        $requestXml->AfterbuyGlobal->addChild("DetailLevel", $detail);
        $requestXml->AfterbuyGlobal->addChild("ErrorLanguage", $language);
        return $requestXml;
    }

}
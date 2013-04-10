<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/

require_once 'WirecardCEE/Client/Request/Abstract.php';

final class WirecardCEE_Client_DataStorage_Request_Initiation
    extends WirecardCEE_Client_Request_Abstract
{
    protected static $RETURN_URL = 'returnUrl';
    protected static $ORDER_IDENT = 'orderIdent';
    protected static $JAVASCRIPT_SCRIPT_VERSION = 'javascriptScriptVersion';
    
    protected $_fingerprintOrderType = 1;
    
    public function __construct($customerId, $shopId, $language, $returnUrl, $secret)
    {
        $this->_setField(self::$RETURN_URL, $returnUrl);
        //for fingerprint calculation there must be at least an empty javascriptScriptVersion
        $this->_setField(self::$JAVASCRIPT_SCRIPT_VERSION, '');
        $this->_setSecret($secret);
        parent::__construct($customerId, $shopId, $language);
    }

    /**
     * setter for parameter javascriptScriptVersion
     * @param type $javascriptVersion 
     */
    public function setJavascriptScriptVersion($javascriptScriptVersion)
    {
        $this->_setField(self::$JAVASCRIPT_SCRIPT_VERSION, $javascriptScriptVersion);
    }

    /**
     * @param string $orderIdent
     * @return WirecardCEE_Client_DataStorage_Response_Initiation 
     */
    public function initiate($orderIdent)
    {
        $this->_setField(self::$ORDER_IDENT, $orderIdent);
        $this->_fingerprintOrder = Array(self::$CUSTOMER_ID, self::$SHOP_ID, self::$ORDER_IDENT, self::$RETURN_URL, self::$LANGUAGE, self::$JAVASCRIPT_SCRIPT_VERSION, self::$SECRET);
        $result = $this->_send();
        require_once 'WirecardCEE/Client/DataStorage/Response/Initiation.php';
        return new WirecardCEE_Client_DataStorage_Response_Initiation($result);
    }
    
    protected function _getRequestUrl()
    {
        require_once 'WirecardCEE/Client/Configuration.php';
        return WirecardCEE_Client_Configuration::loadConfiguration()->getDataStorageUrl().'/init';
    }
}
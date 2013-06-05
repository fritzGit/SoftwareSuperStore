<?php

class Afterbuy_Afterbuycheckout_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
    * Standard-Link to Afterbuys XML API interface
    *
    * @var string
    */
   protected $_ppAfterbuyURI = "https://api.afterbuy.de/afterbuy/ABInterface.aspx";


   /**
    * Simple function, which will send the previously created XML-Request to Afterbuy and returns the
    * result as a SimpleXMLElement.
    *
    * TODO: log errors from Afterbuy with request
    *
    * @param DOMDocument $XMLData
    *
    * @return SimpleXMLElement
    */
   protected function sendRequest( DOMDocument $XMLData )
   {
       $curlHandle = curl_init( $this->_ppAfterbuyURI );
       curl_setopt( $curlHandle, CURLOPT_MUTE, 1 );
       curl_setopt( $curlHandle, CURLOPT_POST, 1 );
       curl_setopt( $curlHandle, CURLOPT_HTTPHEADER, array( 'Content-Type: text/xml' ) );
       curl_setopt( $curlHandle, CURLOPT_POSTFIELDS, $XMLData->saveXML() );
       curl_setopt( $curlHandle, CURLOPT_RETURNTRANSFER, 1 );

       $output = curl_exec( $curlHandle );

       if($output === false)
       {
           print('Curl error: ' . curl_error($curlHandle));
       }

       curl_close( $curlHandle );

       $result = new SimpleXMLElement( $output );

      return $result;
   }
}
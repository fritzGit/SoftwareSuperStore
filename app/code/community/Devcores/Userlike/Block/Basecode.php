<?php
/**
 * @category  Devcores
 * @package   Devcores_Userlike
 * @author    Devcores <info@devcores.com>
 * @author    Sven Gebhardt <sven@devcores.com>
 * @copyright 2012 Devcores UG (haftungsbeschraenkt)
 * @link      http://devcores.com/
 */

class Devcores_Userlike_Block_Basecode extends Mage_Core_Block_Template {
    protected $_secret = null;

    protected function _construct() {
        $this->_secret = Mage::getStoreConfig('userlike/settings/secretkey');
    }
    
    public function isActive() {
        $secret = $this->_secret;
        if (!is_string($secret) || strlen($secret) < 30 || preg_match("/[^a-f0-9]/", $secret)) {
            return false;
        }
        return true;
    }
    
    public function getSecret() {
        return $this->_secret;
    }

}

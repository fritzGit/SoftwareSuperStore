<?php
/**
 * Created by
 * User: jgalvez
 * @author juan galvez
 * @pagage SOFTSS
 * @subpackage SOFTSS_Base
 */
class SOFTSS_Base_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function checkExistDirectory($sDirpath){
        if(is_dir($sDirpath))
            return true;

        return false;
    }

    public function createDirectory($sDirpath){
        if(mkdir($sDirpath, 0777))
           return true;

        return false;
    }

    public function cutStr($text, $pos) {
        if ($pos < strlen($text)) {
        $text = substr($text, 0, $pos);

        if (false !== ($strrpos = strrpos($text,' '))) {
            $text = substr($text, 0, $strrpos);
        }

        $string = $text;
        }

        return $string;
    }

    /**
    * Converts elements divided by newline characters to list items
    * @param String $text
    * @param Array $htmlAttrs
    */
    public function nl2li($text, array $htmlAttrs = null) {
        if (!empty($htmlAttrs)) {
            $attributes = array_walk($htmlAttrs, function($key, $value) {
                return $key.' = "'.$value.'"';
            });

            $openingLi = '<li '.implode(' ', $attributes).'>';
        }
        else
        {
            $openingLi = '<li>';
        }

        $parsedText = '';

        $token = strtok($text, "\n");
        while($token !== false) {
            $parsedText .= $openingLi.$token.'</li>'.PHP_EOL;
            $token = strtok("\n");
        }

        return $parsedText;
    }

    public function getIsHomePage(){
        $page = Mage::app()->getFrontController()->getRequest()->getRouteName();

        if ($page == 'cms'){
            if(Mage::getSingleton('cms/page')->getIdentifier() == Mage::app()->getStore()->getConfig('web/default/cms_home_page'))
                return true;
        }

        return false;
    }
}
?>

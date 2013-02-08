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
}
?>

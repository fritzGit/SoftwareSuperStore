<?php

/*
 *  Software property of Pcfritz.de. Copyright 2013.
 */

/**
 * Breadcrumbs.php (UTF-8)
 *
 * Mar 7, 2013
 * @author Juan Galvez :: juanjogalvez@gmail.com
 * @package SOFTSS
 * @subpackage
 *
 *
 * */
class SOFTSS_Page_Block_Html_Breadcrumbs extends Mage_Page_Block_Html_Breadcrumbs{

    protected $_label = null;

    public function setLabel($label){
        $this->_label = $label;
    }

    public function getLabel(){
        return $this->_label;
    }
}

?>
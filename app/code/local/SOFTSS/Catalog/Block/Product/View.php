<?php

/*
 *  Software property of Pcfritz.de. Copyright 2013.
 */

/**
 * View.php (UTF-8)
 *
 * Apr 8, 2013
 * @author Juan Galvez :: juanjogalvez@gmail.com
 * @package SOFTSS
 * @subpackage
 *
 *
 * */
class SOFTSS_Catalog_Block_Product_View extends Mage_Catalog_Block_Product_View{

    /*
     * Set the heading in the breadcrumb strip.
     */
    protected function _prepareLayout()
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $breadcrumbsBlock->setLabel($this->getProduct()->getName());

        return parent::_prepareLayout();
    }
}

?>
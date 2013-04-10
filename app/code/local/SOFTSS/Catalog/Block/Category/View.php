<?php

/*
 *  Software property of Pcfritz.de. Copyright 2013.
 */

/**
 * View.php (UTF-8)
 *
 * Apr 10, 2013
 * @author Juan Galvez :: juanjogalvez@gmail.com
 * @package SOFTSS
 * @subpackage
 *
 *
 * */
class SOFTSS_Catalog_Block_Category_View extends Mage_Catalog_Block_Category_View{

    /*
     * Set the heading in the breadcrumb strip.
     */
    protected function _prepareLayout()
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $breadcrumbsBlock->setLabel($this->getCurrentCategory()->getName());

        return parent::_prepareLayout();
    }
}

?>
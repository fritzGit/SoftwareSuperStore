<?php

/*
 *  Software property of Pcfritz.de. Copyright 2013.
 */

/**
 * List.php (UTF-8)
 *
 * Mar 6, 2013
 * @author Juan Galvez :: juanjogalvez@gmail.com
 * @package SOFTSS
 * @subpackage
 *
 *
 * */
class SOFTSS_Catalog_Block_Product_List extends Mage_Catalog_Block_Product_List{

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
    protected function _beforeToHtml()
    {
        $toolbar_top = $this->getToolbarBlock();
        $toolbar_bottom = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->_getProductCollection();

        // use sortable parameters
        if ($orders = $this->getAvailableOrders()) {
            $toolbar_top->setAvailableOrders($orders);
            $toolbar_bottom->setAvailableOrders($orders);
        }
        if ($sort = $this->getSortBy()) {
            $toolbar_top->setDefaultOrder($sort);
        }
        if ($dir = $this->getDefaultDirection()) {
            $toolbar_top->setDefaultDirection($dir);
        }
        if ($modes = $this->getModes()) {
            $toolbar_top->setModes($modes);
            $toolbar_bottom->setModes($modes);
        }

        // set collection to toolbars and apply sort
        $toolbar_top->setCollection($collection);
        $toolbar_bottom->setCollection($collection);

        $toolbar_top->setTemplate('catalog/product/list/toolbar_top.phtml');
        $this->setChild('toolbar_top', $toolbar_top);

        $toolbar_bottom->setTemplate('catalog/product/list/toolbar_bottom.phtml');
        $this->setChild('toolbar_bottom', $toolbar_bottom);

        Mage::dispatchEvent('catalog_block_product_list_collection', array(
            'collection' => $this->_getProductCollection()
        ));

        $this->_getProductCollection()->load();

        return parent::_beforeToHtml();
    }
}

?>
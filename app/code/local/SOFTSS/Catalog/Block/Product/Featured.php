<?php

/*
 * Copyright 2013 pcfritz.de Onlinestore GmbH – http://www.pcfritz.de
 * all rights reserved
 */

/**
 * Catalog Product Featured
 *
 * @encoding    UTF-8
 * @package     SOFTSS
 * @subpackage  SOFTSS_Catalog
 * @author      Nikolas Koumarianos <n.entwickler@pcfritz.de>
 */

class SOFTSS_Catalog_Block_Product_Featured extends Mage_Catalog_Block_Product_List {

      /**
     * Product Featured Collection
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected $_productFeaturedCollection;
    
    
  /*
     * Load featured products collection
     * */

    protected function _beforeToHtml() {
        
        $collection = Mage::getResourceModel('catalog/product_collection');

        $attributes = Mage::getSingleton('catalog/config')
                ->getProductAttributes();

        $collection->addAttributeToSelect($attributes)
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addAttributeToFilter('featured_product', 1)
                ->addStoreFilter()
                ->setOrder('created_at desc');

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        $this->_productFeaturedCollection = $collection;

        $this->setFeaturedProductCollection($collection);
        return parent::_beforeToHtml();
    }


}

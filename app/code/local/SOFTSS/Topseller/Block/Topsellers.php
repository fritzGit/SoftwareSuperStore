<?php

/*
 *  Software property of Pcfritz.de. Copyright 2013.
 */

/**
 * Topsellers.php (UTF-8)
 *
 * Feb 15, 2013
 * @author Juan Galvez :: juanjogalvez@gmail.com
 * @package SOFTSS
 * @subpackage
 *
 *
 * */
class SOFTSS_Topseller_Block_Topsellers extends Mage_Catalog_Block_Product_Abstract
{
    const DEFAULT_PRODUCTS_COUNT = 5;

    protected $_topsellerCollection;
    protected $_productsCount;

    public function __construct(int $count = null)
    {
        if(null !== $count)
            $this->setProductsCount($count);

        $this->_setCollection();
    }

    /**
     * Create the topseller collection.
     *
     */
    protected function _setCollection(){
        $storeId = Mage::app()->getStore()->getId();
        $products = Mage::getResourceModel('reports/product_collection')
                    ->addOrderedQty()
                    ->addAttributeToSelect(array('name', 'price', 'small_image', 'short_description', 'description'))
                    ->setStoreId($storeId)
                    ->addStoreFilter($storeId)
                    ->setPageSize($this->getProductsCount())
                    ->setOrder('ordered_qty', 'desc');

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);

        $this->_topsellerCollection = $products;
    }

    public function getCollection(){
        if(null === $this->_topsellerCollection){
            $this->_setCollection();
        }
        return $this->_topsellerCollection;
    }

    /**
     * Set how much product should be displayed at once.
     *
     * @param $count
     * @return Mage_Catalog_Block_Product_New
     */
    public function setProductsCount($count)
    {
        $this->_productsCount = $count;
        return $this;
    }

    /**
     * Get how much products should be displayed at once.
     *
     * @return int
     */
    public function getProductsCount()
    {
        if (null === $this->_productsCount) {
            $this->_productsCount = self::DEFAULT_PRODUCTS_COUNT;
        }
        return $this->_productsCount;
    }
}

?>
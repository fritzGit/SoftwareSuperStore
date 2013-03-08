<?php
/**
 * Description of Addtocart
 *
 * @author J.galvez@pcfritz.de
 */
class SOFTSS_Checkout_Block_Ajax_Addtocart extends Mage_Catalog_Block_Product_Abstract
{
    protected $_productCollection;

    /*
     *
     */
    public function _construct()
    {
        $this->collection_type = Mage::getStoreConfig('checkout/ajaxcartadd/collection_type');
        $this->item_limit = Mage::getStoreConfig('checkout/ajaxcartadd/item_limit');

        $this->_prepareCollection();
    }

    protected function _prepareCollection()
    {
        switch ($this->collection_type)
        {
        case 'related':
          $this->_prepareRelatedProducts($this->product);
          break;
        case 'upsell':
          $this->_prepareUpsellProducts($this->product);
          break;
        case 'promotional':
          $this->_preparePromotionalProducts($this->product);
          break;
        default:
          $this->_prepareCrossellProducts($this->product);
        }
    }

    /**
     * Prepare related items data
     */
    protected function _prepareRelatedProducts($product)
    {
        $this->_productCollection = $product->getRelatedProductCollection()
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->setPositionOrder()
            ->addStoreFilter();

        if($this->_productCollection->count() == 0)
            return;

        if (Mage::helper('catalog')->isModuleEnabled('Mage_Checkout')) {
            //Make collection not to load products that are in specified quote
            Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($this->_productCollection,
                Mage::getSingleton('checkout/session')->getQuoteId()
            );
            // Add all attributes and apply pricing logic to products collection to get correct values in different products lists.
            $this->_addProductAttributesAndPrices($this->_productCollection);
        }

        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_productCollection);

        if ($this->item_limit > 0) {
            $this->_productCollection->setPageSize($this->item_limit);
        }

        $this->_productCollection->load();

        foreach ($this->_productCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }
    }

    /**
     * Prepare crosssell items data
     */
    protected function _prepareCrossellProducts($product)
    {
        $this->_productCollection = $product->getCrossSellProductCollection()
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->setPositionOrder()
            ->addStoreFilter();

        if(count($this->_productCollection) == 0)
            return;

        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_productCollection);

        if ($this->item_limit > 0) {
            $this->_productCollection->setPageSize($this->item_limit);
        }

        $this->_productCollection->load();

        foreach ($this->_productCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }
    }

    /**
     * Prepare upsell items data
     */
    protected function _prepareUpsellProducts($product)
    {
        /* @var $product Mage_Catalog_Model_Product */
        $this->_productCollection = $product->getUpSellProductCollection()
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->setPositionOrder()
            ->addStoreFilter();

        if(count($this->_productCollection) == 0)
            return;

        if (Mage::helper('catalog')->isModuleEnabled('Mage_Checkout')) {
            //Make collection not to load products that are in specified quote
            Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($this->_productCollection,
                Mage::getSingleton('checkout/session')->getQuoteId()
            );
            // Add all attributes and apply pricing logic to products collection to get correct values in different products lists.
            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }

        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_productCollection);

        if ($this->item_limit > 0) {
            $this->_productCollection->setPageSize($this->item_limit);
        }

        $this->_productCollection->load();

        /**
         * Updating collection with desired items
         */
        Mage::dispatchEvent('catalog_product_upsell', array(
            'product'       => $product,
            'collection'    => $this->_productCollection,
            'limit'         => $this->item_limit
        ));

        foreach ($this->_productCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }
    }

    /**
     * Prepare promotional items data
     */
    protected function _preparePromotionalProducts()
    {
        $this->_productCollection = Mage::getResourceModel('catalog/product_collection');

        if(count($this->_productCollection) == 0)
            return;

        // Add all attributes and apply pricing logic to products collection to get correct values in different products lists.
        $this->_addProductAttributesAndPrices($this->_productCollection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_productCollection);

        $this->_productCollection->addAttributeToFilter('promotion', 1)
            ->addStoreFilter();

        if ($this->item_limit > 0) {
            $this->_productCollection->setPageSize($this->item_limit);
        }

        $this->_productCollection->load();
    }

    /**
     * @return collection of Mage_Catalog_Model_Product
     */
    public function getCollection()
    {
        if(!isset($this->_productCollection)){
            $this->_prepareCollection();
        }
        return $this->_productCollection;
    }
}

?>
<?php
/**
 * Created by jgalvez
 */
class SOFTSS_Topseller_Block_Homepagetopsellers extends Amasty_Sorting_Block_Featured
{
    public function getCollection()
    {
        $collection = parent::getCollection();

        $collectionA = array();
        $homepageTopsellersA = array();
        $collectionIdsA = array();

        foreach($collection as $_product){
            $collectionIdsA[] = $_product->getId();
            array_push($collectionA, $_product);

        }

        $collectionA = array_reverse ($collectionA);

        $storeID = Mage::app()->getStore()->getId();
        $homepagetopsellerCollection = Mage::getModel('softsstopseller/homepagetopseller')->getCollection()
                                            ->addFilter('store_id', $storeID);

        $homepagetopsellersA = array();
        foreach($homepagetopsellerCollection as $topsellerProduct){
            $homepagetopsellersA[$topsellerProduct->getPosition()] = $topsellerProduct->getProductId();
        }

        for($i=1; $i<count($collection)+1; $i++)
        {
            if(isset($homepagetopsellersA[$i]) && !in_array($homepagetopsellersA[$i], $collectionIdsA)){
                $productID = $homepagetopsellersA[$i];
                $_product = Mage::getModel( 'catalog/product' )->load($productID);
                array_push($homepageTopsellersA, $_product);
            }else{
                array_push($homepageTopsellersA, array_pop($collectionA));
            }
        }

        return $homepageTopsellersA;
    }
}

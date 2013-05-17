<?php

/*
 *  Software property of Pcfritz.de. Copyright 2013.
 */

/**
 * softdistributionController.php (UTF-8)
 *
 * May 16, 2013
 * @author Juan Galvez :: juanjogalvez@gmail.com
 * @package SOFTSS
 * @subpackage
 *
 *
 * */
class SOFTSS_Catalog_SoftdistributionController extends Mage_Core_Controller_Front_Action
{
    protected $_sSoftdistributionPass = '';
    protected $_sSoftdistributionResellerid = '';
    protected $_aProductShortListURL = "http://test.reseller.softdistribution.net/product_short_list.php?resellerid=[your resellerid]&pass=[your password md5]&cat=Games,Software&territory=en";

    public function indexAction(){
        $aProducts = $this->readShortListXML();
        if(!empty($aProducts))
            $this->insertProducts($aProducts);

    }

    protected function readShortListXML()
    {
        $target =  Mage::getBaseDir(). DIRECTORY_SEPARATOR . 'softdistribution' . DIRECTORY_SEPARATOR . 'testfile.xml';

        if (!is_file($target)) {
            Mage::log('Softdistribution product import failed. No file found.');
            die('file not found');
        }

        $gamesXML = simplexml_load_file($target);

        $aProducts = array();

        $first=true;
        foreach ($gamesXML as $product){
            //skip head node
            if($first){
                $first=false;
                continue;
            }

            $aProduct = array();
            $aProduct['productversionid'] = $product->productversionid;
            $aProduct['publisher'] = $product->publisher;
            $aProduct['full_name'] = $product->full_name;
            $aProduct['productdetailversion'] = $product->productdetailversion;
            $aProduct['availability'] = $product->availability;
            $aProduct['srp'] = $product->srp;
            $aProduct['ppd'] = $product->ppd;
            $aProduct['currency'] = $product->currency;

            $aProducts[] = $aProduct;
        }

        #print_r($aProducts);

        return $aProducts;
    }

    protected function readDetailedListXML()
    {

    }

    private function insertProducts(Array $aProducts){
        $type = 'download';

        //game product attribute set = ?
        $product_attribute_set_id = '?';

        foreach($aProducts as $aProduct){
            $aProductDetail = $this->getProductDetail($aProduct['productversionid']);

            //create categories
            $mainCatID = $this->createCategory($aProductDetail['maincategory']);
            $categories = array($mainCatID);
            if($aProductDetail['subcategory'] != ''){
                $subCatID = $this->createCategory($aProductDetail['subcategory'], $mainCatID);
                $categories[] = $subCatID;
            }

            $product=Mage::getModel('catalog/product')->loadByAttribute('sku', $aProductDetail['ean_esd']);
            #srp Suggested retail price
            #ppd = Published Price to Dealer
            $data = Array(
                'tax_class_id'   => 'UK 17.5%',
                'attribute_set'  => 'Default',
                'status'         => 'Enabled',
                'weight'         => '1',
                'visibility'     => 'Catalogue,Search',
                'short_description' => $aProductDetail['shortdescription'],
                'description'    => $aProductDetail['detaildescription'],
                'qty'            => 100,
                'ordernumber'    => $aProductDetail['productversionid'],
                'name'           => $aProductDetail['name'],
                'manufacturer'   => $aProducts['publisher'],
                'sku'            => $aProductDetail['ean_esd'],
                'price'          => $aProductDetail['srp'],
                'meta_title'     => $aProductDetail['full_name'].' | Softwaresuperstore',
                'meta_description'  => $aProductDetail['teasertext'],
                'meta_keywords'     => $aProductDetail['keywords'],
                'categories'     => $categories,
                '_links_upsell_sku' => $this->getUpsell_Ids($aProductDetail['upsell_productversionid'])
            );

            try {
                if($product instanceof Mage_Catalog_Model_Product){
                    echo 'product exists <br/>';
                    var_dump($data);
                    echo '<br/>';
                    echo $productAPImodel->update($product->getID(),$data, $storeID);
                    echo '<br/>';
                }else{
                    $new_product_id = $productAPImodel->create($type,$product_attribute_set_id,$ean,$data, $storeID);
                    echo "product created. product ID =  $new_product_id<br/>";
                }
            } catch (Exception $e) { // sku already used
                echo 'not created nor updated<br/>';
                Mage::log('product was not able to imported: '.$e->getMessage());
                continue;
            }
        }
    }

    protected function getUpsell_Ids($supplierProductID){
        $product=Mage::getModel('catalog/product')->loadByAttribute('supplierProductID', $$supplierProductID);

        return $product->getId();
    }

    private function createCategory($catname, $parentID = null){
        $storeID = Mage::app()->getStore()->getId();
        $categoryAPImodel = Mage::getModel('catalog/category_api');

        if($parentID == null) $parentID = 2;

        $aCat_nameparent = array('name'=>$catname,'parent' => $parentID);
        $category=$this->loadCategoryByNameAndParent($aCat_nameparent);

        $data = Array(
            'name' => $catname,
            'description' => $catname,
            'is_active' => 1,
            'include_in_menu'   => 1,
            'available_sort_by'=>'position',
            'default_sort_by'=>'position'
        );

        $parentIndex = $i-2;
        if($parentIndex>=0 && isset($mageCategoryID[$parentIndex]))
        {
            $parentID = $mageCategoryID[$parentIndex];
        }
        echo 'name: '.$catname.'<br/>';

        if($category instanceof Mage_Catalog_Model_Category){
            echo 'cat name: '.$category->getName().'<br/>';
            echo 'cat instance of Category<br/>';
            $catId = $category->getId();
            #$categoryAPImodel->update($catId, $data, $storeID);
        }else{
            $catId=$categoryAPImodel->create($parentID, $data, $storeID);
        }

        return $catId;
    }

    protected function loadCategoryByNameAndParent(Array $cat_nameparent){
        $collection = Mage::getModel('catalog/category')->getResourceCollection()
            ->addAttributeToSelect('id')
            ->addAttributeToFilter('name', $cat_nameparent['name'])
            ->addAttributeToFilter('parent', $cat_nameparent['parent'])
            ->setPage(1,1);

        foreach ($collection as $object) {
            return $object;
        }
        return false;
    }

    private function getProductDetail($productversionid)
    {
        $url = "http://test.reseller.softdistribution.net/product_details.php?resellerid=[your resellerid]&pass=[your password]&id=$productversionid";
        $productXML = $this->getXML($url);

        if(isset($productXML))
            $productDetailXML = simplexml_load_file($productDetailXML);
        else
            return null;

        $aProductDetail = array();

        $first=true;
        foreach ($productDetailXML as $productData){
            //skip head node
            if($first){
                $first=false;
                continue;
            }

            $aProductDetail['productid'] = $productData->productid;
            $aProductDetail['productversionid'] = $productData->productversionid;
            $aProductDetail['upsell_productversionid'] = $productData->upsell_productversionid;
            $aProductDetail['productdetailversion'] = $productData->productdetailversion;
            $aProductDetail['name'] = $productData->name;
            $aProductDetail['full_name'] = $productData->full_name;
            $aProductDetail['agerating'] = $productData->agerating;
            $aProductDetail['subtitle'] = $productData->subtitle;
            $aProductDetail['teasertext'] = $productData->teasertext;
            $aProductDetail['shortdescription'] = $productData->shortdescription;
            $aProductDetail['extendeddescription'] = $productData->extendeddescription;
            $aProductDetail['detaildescription'] = $productData->detaildescription;

            $aFeatures = array();
            foreach($productData->features as $feature){
                $aFeatures[] = $feature;
            }
            $aProductDetail['features']  = $aFeatures;

            $aMarketingclaims= array();
            foreach($productData->marketingclaims as $marketingclaim){
                $aMarketingclaims[] = $marketingclaim;
            }
            $aProductDetail['marketingclaims'] = $aMarketingclaims;

            //images (there are more...)
            $aProductDetail['boxshot2d_large'] = $productData->boxshot2d_large;

            $aProductDetail['versiontype'] = $productData->versiontype;
            $aProductDetail['mastersize'] = $productData->productversionid;
            $aProductDetail['serialnumber_required'] = $productData->productversionid;
            $aProductDetail['publisher'] = $productData->productversionid;
            $aProductDetail['platform'] = $productData->platform;
            $aProductDetail['ean_esd'] = $productData->ean_esd;
            $aProductDetail['ean_box'] = $productData->ean_box;
            $aProductDetail['releasedateactual'] = $productData->releasedateactual;

            foreach($productData->sysrequirements as $sysrequirement){
                $aOS = array();
                foreach($sysrequirement->sros as $sros){
                    $aOS[] = $sros;
                }
                if(isset($aOS))
                    $aProductDetail['sysrequirements'] = implode(',', $aOS).'<br/>';
                if(isset($productData->srprocessor))
                    $aProductDetail['sysrequirements'] = $sysrequirement->srprocessor.'<br/>';
                if(isset($sysrequirement->srram))
                    $aProductDetail['sysrequirements'] .= $sysrequirement->srram.'<br/>';
                if(isset($sysrequirement->srharddrive))
                    $aProductDetail['sysrequirements'] .= $sysrequirement->srharddrive.'<br/>';
                if(isset($sysrequirement->srminresolution))
                    $aProductDetail['sysrequirements'] .= $sysrequirement->srminresolution.'<br/>';
            }

            $aProductDetail['usernumber'] = $productData->usernumber;
            $aProductDetail['userlicenseterm'] = $productData->userlicenseterm;
            $aProductDetail['language'] = $productData->language;
            $aProductDetail['availability'] = $productData->availability;
            $aProductDetail['srp'] = $productData->srp;
            $aProductDetail['ppd'] = $productData->ppd;
            $aProductDetail['currency'] = $productData->currency;
            $aProductDetail['genre'] = $productData->genre;
            $aProductDetail['maincategory'] = $productData->maincategory;
            $aProductDetail['subcategory'] = $productData->subcategory;
            $aProductDetail['keywords'] = $productData->keywords;
            $aProductDetail['extendedorder_required'] = $productData->extendedorder_required;
            $aProductDetail['backupcd_available'] = $productData->backupcd_available;
        }

        return $aProductDetail;
    }

    /**
     * Simple function, which will get an xml file from softdestribution
     *
     * @param String url
     *
     * @return XMLDoc
     */
    protected function getXML($url)
    {
        // Get cURL resource
        $curl = curl_init();
        // Set some options - we are passing in a useragent too here
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => 'cURL Request'
        ));

        // Send the request & save response to $resp
        $responce = curl_exec($curl);

        if(curl_errno($curl)){
            Mage::log(curl_error($curl));
            echo 'something went wrong...'.curl_error($curl);
        }

        // Close request to clear up some resources
        curl_close($curl);

        return $responce;
    }
}

?>
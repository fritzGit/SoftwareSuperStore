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
class SOFTSS_Import_SoftdistributionController extends Mage_Core_Controller_Front_Action
{
  
    const XML_SOFTDISTIBUTION_RESELLERID = 'checkout/softdistribution/resellerid';
    const XML_SOFTDISTIBUTION_PASSWORD = 'checkout/softdistribution/password';
    const XML_SOFTDISTIBUTION_IMPORT_URL = 'checkout/softdistribution/url_import';
    const XML_SOFTDISTIBUTION_IMPORT_CATEGORY = 'checkout/softdistribution/import_category';
    const XML_SOFTDISTIBUTION_IMPORT_TERRITORY = 'checkout/softdistribution/url_import';
    const XML_SOFTDISTIBUTION_IMPORT_PRODUCT_DETAILS = 'checkout/softdistribution/url_import_product_detail';
    
    protected $_logFileName = 'import.log';
    protected $_productType =  Mage_Catalog_Model_Product_Type::TYPE_SIMPLE;
    protected $_attributeSetId = 4; //Default
    protected $_gameCategoryId = 16; //Games Category Id
    protected $_storeId;
    

    public function indexAction()
    {           
        
        $this->_storeId = Mage::app()->getWebsite()->getDefaultGroup()->getDefaultStoreId();
        
        Mage::log("Import Started", null, $this->_logFileName);        
        
        $aProducts = $this->readShortListXML();
        if(!empty($aProducts)) {
              $this->insertProducts($aProducts);
        }
        
        Mage::log("Import Finished", null, $this->_logFileName);        

    }

    protected function readShortListXML()
    {
        
        $url = Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_IMPORT_URL);
        $url .= '?resellerid='.Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_RESELLERID);
        $url .= '&pass='.md5(Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_PASSWORD));
        $url .= '&cat='.Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_IMPORT_CATEGORY);
        $url .= '&territory='.Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_IMPORT_TERRITORY);
         
        $aProducts = array();
        
        $target =  Mage::getBaseDir(). DIRECTORY_SEPARATOR . 'softdistribution' . DIRECTORY_SEPARATOR . 'product_short_list3.xml';

        if (!is_file($target)) {
            Mage::log('Softdistribution product import failed. No file found.');
            die('file not found');
        } 
    
        /*$dataXML = $this->getXML($url);

        if(isset($dataXML)) { */
            $gamesXML = simplexml_load_file($target);
     /*   } else {
            Mage::log("No xml response. Import failed", null, $this->_logFileName);     
            return $aProducts;      
        }     */

        $first = true;
        foreach ($gamesXML as $product) {
            //skip head node
            if ($first) {
                $first = false;
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

        return $aProducts;
    }
   
    protected function insertProducts(Array $aProducts)
    {              

        $productAPImodel = Mage::getModel('catalog/product_api');
        
        foreach ($aProducts as $aProduct) {
                        
            $aProductDetail = $this->getProductDetail($aProduct['productversionid']);                    
            
            //create categories
            $mainCatID = $this->createCategory($aProductDetail['maincategory']);
            $categories = array($this->_gameCategoryId, $mainCatID);
            if($aProductDetail['subcategory'] != ''){
                $subCatID = $this->createCategory($aProductDetail['subcategory'], $mainCatID);
                $categories[] = $subCatID;
            }
        
            $sku = $aProductDetail['ean_esd'] != '' ? $aProductDetail['ean_esd'] : $aProductDetail['ean_box'];
            
            if ($sku == '') {
                Mage::log("Product with no SKU - EAN skipped: ".$aProductDetail['name'], null, $this->_logFileName);      
                continue;
            }
            
            $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);         
                                                
            #srp Suggested retail price
            #ppd = Published Price to Dealer
            $data = Array(
                'tax_class_id'                  => '5',
                'status'                        => 'Enabled',
                'weight'                        => '1',
                'status'                        => '1',
                'visibility'                    => '4', //Catalog, Search
                'short_description'             => $aProductDetail['shortdescription'],
                'description'                   => $aProductDetail['detaildescription'],
                'stock_data'                    => array('qty' => 100000, 'is_in_stock' => $this->getIsInStock($aProductDetail['availability']), 'manage_stock' => 1),
                'softss_features'               => $aProductDetail['features'],
                'name'                          => $aProductDetail['name'],
                'publisher'                     => $aProductDetail['publisher'],
                'programmversion'               => $aProductDetail['programmversion'],
                'sku'                           => $sku,
                'price'                         => $aProductDetail['srp'],
                'meta_title'                    => $aProductDetail['full_name'].' | Softwaresuperstore',
                'meta_description'              => $aProductDetail['teasertext'],
                'meta_keyword'                 => $aProductDetail['keywords'],
                'categories'                    => $categories,
                'softss_system_requirements'    => $aProductDetail['systemrequirements'],
                'image_label'                   => '',
                'small_image_label'             => '',
                'thumbnail_label'               => '',
                'platform'                      => $aProductDetail['platform'],
                'versiontype'                   => $aProductDetail['versiontype'],
                'softss_mastersize'             => $aProductDetail['mastersize'],
                'softss_serialnumber_required'  => $aProductDetail['serialnumber_required'] == "yes" ? 1 : 0,
                'softss_release_date_actual'    => $aProductDetail['releasedateactual'],
                'softss_extendedorder_required' => $aProductDetail['extendedorder_required'],
                'softss_backup_cd_available'    => $aProductDetail['backupcd_available'],
                'language_version'              => $aProductDetail['language_version'],
                'softss_supplier_product_id'    => $aProductDetail['productversionid'],           
                'softss_multiplayer'            => $aProductDetail['multiplayer'] == "yes" ? 1 : 0,
                'softss_drm_type'               => $aProductDetail['drmtype'],
                'softss_product_video'          => $aProductDetail['video']
                //'_links_upsell_sku' => $this->getUpsellIds($aProductDetail['upsell_productversionid'])
            );
            
            try {                   
                if($product instanceof Mage_Catalog_Model_Product){
                    $productAPImodel->update($product->getId(), $data, $this->_storeId);
                    Mage::log("Product ".$product->getName()." updated: ".$product->getId(), null, $this->_logFileName);   
                }else{            
                    $newProductId = $productAPImodel->create($this->_productType, $this->_attributeSetId, $sku, $data, $this->_storeId);
                    $this->addImages($newProductId, $aProductDetail);
                    Mage::log("New Product ".$aProductDetail['name']." created: ".$newProductId, null, $this->_logFileName);   
                }
            } catch (Exception $e) { // sku already used                    
                Mage::log("Product ".$aProductDetail['name']." was not able to imported: ".$e->getMessage(), null, $this->_logFileName);   
            }                 
          
        }
    }

    protected function getUpsellIds($supplierProductID){
        
        $product = Mage::getModel('catalog/product')->loadByAttribute('softss_supplier_product_id', $supplierProductID);

        if($product instanceof Mage_Catalog_Model_Product){
           return $product->getId();
        }
         return;        
    }

    protected function createCategory($catname, $parentID = null){
                
        $categoryAPImodel = Mage::getModel('catalog/category_api');

        if($parentID == null) $parentID = $this->_gameCategoryId;

        $aCat_nameparent = array('name'=>$catname,'parent' => $parentID);
            
        $category = $this->loadCategoryByNameAndParent($aCat_nameparent);

     
        $data = Array(
            'name' => $catname,
            'description' => $catname,
            'is_active' => 1,
            'include_in_menu'   => 1,
            'available_sort_by'=>'position',
            'default_sort_by'=>'position'
        );  
        
        if($category instanceof Mage_Catalog_Model_Category && $category->getId()) {        
            $catId = $category->getId();   
            $categoryAPImodel->update($catId, $data, $this->_storeId);
            Mage::log("Category ".$catname." updated: ".$catId, null, $this->_logFileName);  

        }else{          
            $catId = $categoryAPImodel->create($parentID, $data, $this->_storeId);
            Mage::log("Category ".$catname." created: ".$catId, null, $this->_logFileName);   
        }                     
        
        return $catId;
    }

    protected function loadCategoryByNameAndParent(Array $cat_nameparent){
        
        $category = Mage::getModel('catalog/category')->getResourceCollection()
            ->addAttributeToSelect('id')
            ->addAttributeToFilter('name', $cat_nameparent['name'])
            ->addAttributeToFilter('parent_id', $cat_nameparent['parent'])
            ->getFirstItem();
                     
        if($category instanceof Mage_Catalog_Model_Category){
            return $category;
        }
        return false;
    }

    private function getProductDetail($productversionid)
    {
        
        $url = Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_IMPORT_PRODUCT_DETAILS);
        $url .= '?resellerid='.Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_RESELLERID);
        $url .= '&pass='.md5(Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_PASSWORD));
        $url .= '&id='.$productversionid;
        
        $aProductDetail = array();

       $productXML = $this->getXML($url);

        if(isset($productXML)) {
            $productDetailXML = simplexml_load_string($productXML, null, LIBXML_NOCDATA);
        } else {
            Mage::log("No xml product details response for product with productversion".$productversionid, null, $this->_logFileName);     
            return $aProductDetail;      
        }     
        

        $first = true;              
 
        foreach ($productDetailXML as $productData) {
            
            //skip head node
            if ($first) {
                $first = false;
                continue;
            }

            //$aProductDetail['productid'] = $productData->productid;
            $aProductDetail['productversionid'] = $productData->productversionid;
            //$aProductDetail['upsell_productversionid'] = $productData->upsell_productversionid;
            $aProductDetail['productdetailversion'] = $productData->productdetailversion;
            $aProductDetail['name'] = $productData->name;
            $aProductDetail['full_name'] = $productData->full_name;
            $aProductDetail['programmversion'] = $productData->programmversion;
            //$aProductDetail['agerating'] = $productData->agerating;
            $aProductDetail['subtitle'] = $productData->subtitle;
            $aProductDetail['teasertext'] = $productData->teasertext;
            $aProductDetail['shortdescription'] = $productData->shortdescription;
            $aProductDetail['extendeddescription'] = $productData->extendeddescription;
            $aProductDetail['detaildescription'] = $productData->detaildescription;
            $aProductDetail['drmtype'] = $productData->drmtype;
            $aProductDetail['multiplayer'] = $productData->multiplayer;

            $aFeatures = array();
            foreach($productData->features->feature as $feature){
                
               $aFeatures[] = (string) $feature;
            }            
            
            $aProductDetail['features']  = implode(',', $aFeatures);

            
            $aMarketingclaims= array();
            foreach($productData->marketingclaims->marketingclaim as $marketingclaim){
                $aMarketingclaims[] = $marketingclaim;
            }
            $aProductDetail['marketingclaims'] = $aMarketingclaims; 

            //images (there are more...)
            $aProductDetail['boxshot2d_large'] = $productData->boxshot2d_large;
            $aProductDetail['boxshot3d_large'] = $productData->boxshot3d_large;

            //images screenshots
            $i = 1;
            foreach($productData->screenshots->screenshot as $screenshot){                
                $aProductDetail['screenshot_'.$i] = $screenshot;
                $i++;
            } 
            
            $aProductDetail['screenshot_total'] = $i-1;

            $aProductDetail['versiontype'] = $productData->versiontype;
            $aProductDetail['mastersize'] = $productData->mastersize;
            $aProductDetail['serialnumber_required'] = $productData->serialnumber_required;
            $aProductDetail['publisher'] = $productData->publisher;
            $aProductDetail['platform'] = $productData->platform;
            $aProductDetail['ean_esd'] = $productData->ean_esd;
            $aProductDetail['ean_box'] = $productData->ean_box;
            $aProductDetail['releasedateactual'] = $productData->releasedateactual;
                 
            $aProductDetail['systemrequirements'] = $productData->systemrequirements;                                  
            
            if(!$aProductDetail['systemrequirements']) {                
                
                foreach ($productData->sysrequirements->sysrequirement as $sysrequirement) {
                
                    $aOS = array();                  
                    foreach($sysrequirement->sros as $sros){
                        $aOS[] = $sros;
                    }

                    if(isset($aOS))
                        $aProductDetail['systemrequirements'] = implode(',', $aOS).',';
                    if(isset($productData->srprocessor))
                        $aProductDetail['systemrequirements'] .= $sysrequirement->srprocessor.' Processor,';
                    if(isset($sysrequirement->srram))
                        $aProductDetail['systemrequirements'] .= $sysrequirement->srram.' RAM,';
                    if(isset($sysrequirement->srharddrive))
                        $aProductDetail['systemrequirements'] .= $sysrequirement->srharddrive.' Screen resolution,';
                    if(isset($sysrequirement->srminresolution))
                        $aProductDetail['systemrequirements'] .= $sysrequirement->srminresolution.',';
                     if(isset($sysrequirement->srother))
                        $aProductDetail['systemrequirements'] .= $sysrequirement->srother.',';
                }                   
                
            }

            $aProductDetail['usernumber'] = $productData->usernumber;
            $aProductDetail['userlicenseterm'] = $productData->userlicenseterm;
            $aProductDetail['language_version'] = $productData->language;
            $aProductDetail['availability'] = $productData->availability;
            $aProductDetail['srp'] = $productData->srp;
            $aProductDetail['ppd'] = $productData->ppd;
            //$aProductDetail['currency'] = $productData->currency;
            //$aProductDetail['genre'] = $productData->genre;
            $aProductDetail['maincategory'] = $productData->maincategory;
            $aProductDetail['subcategory'] = $productData->subcategory;
            $aProductDetail['keywords'] = $productData->keywords;
            $aProductDetail['extendedorder_required'] = $productData->extendedorder_required;
            $aProductDetail['backupcd_available'] = $productData->backupcd_available;
                        
            $this->addVideo($productData);            
      
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
            Mage::log("Curl error".curl_error($curl), null, $this->_logFileName);   
        }

        // Close request to clear up some resources
        curl_close($curl);

        return $responce;
    }
    
    protected function getIsInStock($availability) 
    {
        $codes = array("N_A","OOS","PRE_SELL","PRE_SELL_ONLY","PRE_SELL_BONUS","DELISTED");
        
        if(in_array($availability, $codes)) {
            return false;
        }        
        return true;        

    }
    
    protected function addVideo($productData) 
    {
          if ($videoType = $productData->videos->video->type) {            

            $aProductDetail['video'] = $aProductDetail['productversionid'].'.'.$videoType;

            //$url  = 'http://cdn.download04.de/videos/P03214/P03214-01_Emergency_2012_Deluxe_NEU_Trailer__Trailer_462b6_Website_h264_240p.flv';

            $url = $productData->videos->video->filename;

            $path = Mage::getBaseDir('media').DS.'video'.DS;

            if ( !file_exists($path)) {
                mkdir($path);
            }               

            $data = $this->getXML($url);

            if($data) {            
                file_put_contents($path.$aProductDetail['video'], $data);            
            }                

        }
    }
    
    protected function addImages($productId, $data) 
    {        
        
        // Remove unset images, add image to gallery if exists
        $importDir = Mage::getBaseDir('media') . DS . 'import'.DS. 'tmp'.DS. $data['productversionid']. DS;

        if ( !file_exists($importDir)) {
            mkdir($importDir);
        } 
        
        $usernamePass = '?resellerid='.Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_RESELLERID).'&pass='.md5(Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_PASSWORD));
        
        if ($data['boxshot3d_large']) {
            
            $imgData = $this->getXML($data['boxshot3d_large'].$usernamePass);

            if($data) {            
                file_put_contents($importDir.$data['productversionid'].'_2d', $imgData);   
                $imgPath = $data['productversionid'].'_2d';
            } 
        } elseif($data['boxshot2d_large']){
            
            $imgData = $this->getXML($data['boxshot2d_large'].$usernamePass);

            if($data) {            
                file_put_contents($importDir.$data['productversionid'].'_3d', $imgData);   
                $imgPath = $data['productversionid'].'_3d';
            } 
        }
        
        $aImgPathScreenShot = array();
        
        for ($i=1;$i<=$data['screenshot_total'];$i++) {
                         
            $imgData = $this->getXML($data['screenshot_'.$i].$usernamePass);

            if($data) {            
                file_put_contents($importDir.$data['productversionid'].'_s'.$i, $imgData);   
                $aImgPathScreenShot[] = $data['productversionid'].'_s'.$i;
            }             
        }        
        
        $product = Mage::getModel('catalog/product')->load($productId);          
        
        // Add three image sizes to media gallery
        $mediaArray = array(
            'thumbnail'   => $imgPath,
            'small_image' => $imgPath,
            'image'       => $imgPath,
        );
                   
        
        foreach($mediaArray as $imageType => $fileName) {
            $filePath = $importDir.$fileName;
            if ( file_exists($filePath) ) {
                try {
                    $product->addImageToMediaGallery($filePath, $imageType, false);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            } else {
                echo "Product does not have an image or the path is incorrect. Path was:".$filePath."<br/>";
            }
        }
        
        //add screenshots as additional images
        foreach($aImgPathScreenShot as $imgPathScreenShot) {
            $filePath = $importDir.$imgPathScreenShot;
            if ( file_exists($filePath) ) {
                try {
                    $product->addImageToMediaGallery($filePath);
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            } else {
                echo "Product does not have an image or the path is incorrect. Path was:".$filePath."<br/>";
            }
        }
        
        $product->save();  
        $this->delTree($importDir);
        
    }
    
    protected function delTree($dir) {
        $files = glob($dir . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (substr($file, -1) == '/')
                delTree($file);
            else
                unlink($file);
        }
        rmdir($dir);
    }

}


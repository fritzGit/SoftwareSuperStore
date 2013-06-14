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
class SOFTSS_Import_SoftdistributionController extends Mage_Core_Controller_Front_Action {

    const XML_SOFTDISTIBUTION_RESELLERID = 'checkout/softdistribution/resellerid';
    const XML_SOFTDISTIBUTION_PASSWORD = 'checkout/softdistribution/password';
    const XML_SOFTDISTIBUTION_IMPORT_URL = 'checkout/softdistribution/url_import';
    const XML_SOFTDISTIBUTION_IMPORT_CATEGORY = 'checkout/softdistribution/import_category';
    const XML_SOFTDISTIBUTION_TERRITORY = 'checkout/softdistribution/territory';
    const XML_SOFTDISTIBUTION_IMPORT_PRODUCT_DETAILS = 'checkout/softdistribution/url_import_product_detail';

    protected $_logFileName = 'import.log';
    protected $_productType = Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE;
    protected $_attributeSetId = 17; //Games for live 12
    protected $_gameCategoryId = 16; //Games Category Id for live 4
    protected $_storeId;

    public function indexAction() {

        $this->_storeId = Mage::app()->getWebsite()->getDefaultGroup()->getDefaultStoreId();

        Mage::log("Import Started", null, $this->_logFileName);

        $aProducts = $this->readShortListXML();
        if (!empty($aProducts)) {
            $this->insertProducts($aProducts);
        }

        $this->deactivateCategories();

        Mage::log("Import Finished", null, $this->_logFileName);
    }

    protected function readShortListXML() {

        $url = Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_IMPORT_URL);
        $url .= '?resellerid=' . Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_RESELLERID);
        $url .= '&pass=' . md5(Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_PASSWORD));
        $url .= '&cat=' . Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_IMPORT_CATEGORY);
        $url .= '&territory=' . Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_TERRITORY);

        $aProducts = array();

        /* $target =  Mage::getBaseDir(). DIRECTORY_SEPARATOR . 'softdistribution' . DIRECTORY_SEPARATOR . 'product_short_list3.xml';

          if (!is_file($target)) {
          Mage::log('Softdistribution product import failed. No file found.');
          die('file not found');
          } */

        $dataXML = $this->getXML($url);

        if (isset($dataXML)) {
            $gamesXML = simplexml_load_string($dataXML);
        } else {
            Mage::log("No xml response. Import failed", null, $this->_logFileName);
            return $aProducts;
        }

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

    protected function insertProducts(Array $aProducts) {

        $productAPImodel = Mage::getModel('catalog/product_api');

        foreach ($aProducts as $aProduct) {

            $aProductDetail = $this->getProductDetail($aProduct['productversionid']);

            $sku = $aProductDetail['ean_esd'] != '' ? $aProductDetail['ean_esd'] : $aProductDetail['ean_box'];

            if ($sku == '' || $aProductDetail['currency'] != 'GBP') {
                Mage::log("Product with no SKU - EAN or curency not GBP skipped: " . $aProductDetail['name'] . '---' . $aProductDetail['productversionid'], null, $this->_logFileName);
                continue;
            }

            //create categories
            $mainCatID = $this->createCategory($aProductDetail['maincategory']);
            $categories = array($this->_gameCategoryId, $mainCatID);
            if ($aProductDetail['subcategory'] != '') {
                $subCatID = $this->createCategory($aProductDetail['subcategory'], $mainCatID);
                $categories[] = $subCatID;
            }
            $product = Mage::getModel('catalog/product')->loadByAttribute('softss_supplier_product_id', $aProduct['productversionid']);

            $downloadData = array();
            $downloadData['link'][0] = array(
                'is_delete' => '',
                'link_id' => '0',
                'title' => 'Download',
                'price' => '',
                'number_of_downloads' => '0',
                'is_shareable' => '2',
                'is_unlimited' => '1',
                'sample' => array(
                    'file' => '[]',
                    'type' => '',
                    'url' => ''
                ),
                'file' => '[]',
                'type' => 'url',
                'link_url' => 'www.download.com',
                'sort_order' => ''
            );


            #srp Suggested retail price
            #ppd = Published Price to Dealer
            $data = Array(
                'tax_class_id' => '5',
                'status' => 'Enabled',
                'weight' => '1',
                'status' => '1',
                'visibility' => '4', //Catalog, Search
                'short_description' => $aProductDetail['shortdescription'],
                'description' => $aProductDetail['detaildescription'],
                'stock_data' => array('qty' => 100000, 'is_in_stock' => $this->getIsInStock($aProductDetail['availability']), 'manage_stock' => 1),
                'softss_features' => $aProductDetail['features'],
                'name' => $aProductDetail['name'],
                'publisher' => $aProductDetail['publisher'],
                'programmversion' => $aProductDetail['programmversion'],
                'sku' => $sku,
                'price' => $aProductDetail['srp'],
                'meta_title' => $aProductDetail['full_name'] . ' | Softwaresuperstore',
                'meta_description' => $aProductDetail['teasertext'],
                'meta_keyword' => $aProductDetail['keywords'],
                'categories' => $categories,
                'softss_system_requirements' => $aProductDetail['systemrequirements'],
                'image_label' => $aProductDetail['name'],
                'small_image_label' => $aProductDetail['name'],
                'thumbnail_label' => $aProductDetail['name'],
                'platform' => $aProductDetail['platform'],
                'versiontype' => $aProductDetail['versiontype'],
                'softss_mastersize' => $aProductDetail['mastersize'],
                'softss_serialnumber_required' => $aProductDetail['serialnumber_required'] == "yes" ? 1 : 0,
                'softss_release_date_actual' => $aProductDetail['releasedateactual'],
                'softss_extendedorder_required' => $aProductDetail['extendedorder_required'],
                'softss_backup_cd_available' => $aProductDetail['backupcd_available'],
                'language_version' => $aProductDetail['language_version'],
                'softss_supplier_product_id' => $aProductDetail['productversionid'],
                'softss_multiplayer' => $aProductDetail['multiplayer'] == "yes" ? 1 : 0,
                'softss_drm_type' => $aProductDetail['drmtype'],
                'softss_product_video' => $aProductDetail['video'],
                '_links_upsell_sku' => $this->getUpsellIds($aProductDetail['upsell_productversionid']),
                'links_title' => 'Download',
                'links_purchased_separately' => '0'
            );

            try {
                if ($product instanceof Mage_Catalog_Model_Product && $product->getId()) {
                    $productAPImodel->update($product->getId(), $data, $this->_storeId);
                    
                    $_images = Mage::getModel('catalog/product')->load($product->getId())->getMediaGalleryImages();
                    if(!$_images) {
                       $this->addImages($product->getId(), $aProductDetail);
                    }
                    
                    Mage::log("Product " . $product->getName() . " updated: " . $product->getId(), null, $this->_logFileName);
                } else {
                    $newProductId = $productAPImodel->create($this->_productType, $this->_attributeSetId, $sku, $data, $this->_storeId);
                    $this->addImages($newProductId, $aProductDetail);
            
                    //add downloadable link    
                    $product = Mage::getModel('catalog/product')->load($newProductId);
                    $product->setDownloadableData($downloadData);
                    $product->save();

                    Mage::log("New Product " . $aProductDetail['name'] . " created: " . $newProductId, null, $this->_logFileName);
                }
            } catch (Exception $e) { // sku already used                    
                Mage::log("Product " . $aProductDetail['name'] . " was not able to imported: " . $e->getMessage(), null, $this->_logFileName);
            }
        }
    }

    protected function getUpsellIds($supplierProductID) {

        $product = Mage::getModel('catalog/product')->loadByAttribute('softss_supplier_product_id', $supplierProductID);

        if ($product instanceof Mage_Catalog_Model_Product && $product->getId()) {

            $param = array(
                $product->getId() => array(
                    'position' => '1'
                )
            );

            return $param;
        }
        return;
    }

    protected function createCategory($catname, $parentID = null) {

        $categoryAPImodel = Mage::getModel('catalog/category_api');

        if ($parentID == null)
            $parentID = $this->_gameCategoryId;

        $aCat_nameparent = array('name' => $catname, 'parent' => $parentID);

        $category = $this->loadCategoryByNameAndParent($aCat_nameparent);

        $data = Array(
            'name' => $catname,
            'description' => $catname,
            'is_active' => 1,
            'include_in_menu' => 1,
            'available_sort_by' => 'position',
            'default_sort_by' => 'position'
        );

        if ($category instanceof Mage_Catalog_Model_Category && $category->getId()) {
            $catId = $category->getId();
            $categoryAPImodel->update($catId, $data, $this->_storeId);
            Mage::log("Category " . $catname . " updated: " . $catId, null, $this->_logFileName);
        } else {
            $catId = $categoryAPImodel->create($parentID, $data, $this->_storeId);
            Mage::log("Category " . $catname . " created: " . $catId, null, $this->_logFileName);
        }

        return $catId;
    }

    protected function loadCategoryByNameAndParent(Array $cat_nameparent) {

        $category = Mage::getModel('catalog/category')->getResourceCollection()
                ->addAttributeToSelect('id')
                ->addAttributeToFilter('name', $cat_nameparent['name'])
                ->addAttributeToFilter('parent_id', $cat_nameparent['parent'])
                ->getFirstItem();

        if ($category instanceof Mage_Catalog_Model_Category) {
            return $category;
        }
        return false;
    }

    private function getProductDetail($productversionid) {

        $url = Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_IMPORT_PRODUCT_DETAILS);
        $url .= '?resellerid=' . Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_RESELLERID);
        $url .= '&pass=' . md5(Mage::getStoreConfig(self::XML_SOFTDISTIBUTION_PASSWORD));
        $url .= '&id=' . $productversionid;

        $aProductDetail = array();

        $productXML = $this->getXML($url);

        if (isset($productXML)) {
            $productDetailXML = simplexml_load_string($productXML, null, LIBXML_NOCDATA);
        } else {
            Mage::log("No xml product details response for product with productversion" . $productversionid, null, $this->_logFileName);
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
            $aProductDetail['upsell_productversionid'] = $productData->upsell_productversionid;
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
            foreach ($productData->features->feature as $feature) {

                $aFeatures[] = (string) $feature;
            }

            $aProductDetail['features'] = implode(',', $aFeatures);


            $aMarketingclaims = array();
            foreach ($productData->marketingclaims->marketingclaim as $marketingclaim) {
                $aMarketingclaims[] = $marketingclaim;
            }
            $aProductDetail['marketingclaims'] = $aMarketingclaims;

            //images (there are more...)
            $aProductDetail['boxshot2d_large'] = $productData->boxshot2d_large;
            $aProductDetail['boxshot3d_large'] = $productData->boxshot3d_large;

            //images screenshots
            $i = 1;
            foreach ($productData->screenshots->screenshot as $screenshot) {
                $aProductDetail['screenshot_' . $i] = $screenshot;
                $i++;
            }

            $aProductDetail['screenshot_total'] = $i - 1;

            $aProductDetail['versiontype'] = $productData->versiontype;
            $aProductDetail['mastersize'] = $productData->mastersize;
            $aProductDetail['serialnumber_required'] = $productData->serialnumber_required;
            $aProductDetail['publisher'] = $productData->publisher;
            $aProductDetail['platform'] = $productData->platform;
            $aProductDetail['ean_esd'] = $productData->ean_esd;
            $aProductDetail['ean_box'] = $productData->ean_box;
            $aProductDetail['releasedateactual'] = $productData->releasedateactual;

            $aProductDetail['systemrequirements'] = $productData->systemrequirements;

            if (!$aProductDetail['systemrequirements']) {

                foreach ($productData->sysrequirements->sysrequirement as $sysrequirement) {

                    $aOS = array();
                    foreach ($sysrequirement->sros as $sros) {
                        $aOS[] = $sros;
                    }

                    if (isset($aOS))
                        $aProductDetail['systemrequirements'] = implode(',', $aOS) . ',';
                    if (isset($productData->srprocessor))
                        $aProductDetail['systemrequirements'] .= $sysrequirement->srprocessor . ' Processor,';
                    if (isset($sysrequirement->srram))
                        $aProductDetail['systemrequirements'] .= $sysrequirement->srram . ' RAM,';
                    if (isset($sysrequirement->srharddrive))
                        $aProductDetail['systemrequirements'] .= $sysrequirement->srharddrive . ' Screen resolution,';
                    if (isset($sysrequirement->srminresolution))
                        $aProductDetail['systemrequirements'] .= $sysrequirement->srminresolution . ',';
                    if (isset($sysrequirement->srother))
                        $aProductDetail['systemrequirements'] .= $sysrequirement->srother . ',';
                }
            }

            $aProductDetail['usernumber'] = $productData->usernumber;
            $aProductDetail['userlicenseterm'] = $productData->userlicenseterm;
            $aProductDetail['language_version'] = $productData->language;
            $aProductDetail['availability'] = $productData->availability;
            $aProductDetail['srp'] = $productData->srp;
            $aProductDetail['ppd'] = $productData->ppd;
            $aProductDetail['currency'] = $productData->currency;
            //$aProductDetail['genre'] = $productData->genre;
            $aProductDetail['maincategory'] = $productData->maincategory;
            $aProductDetail['subcategory'] = $productData->subcategory;
            $aProductDetail['keywords'] = $productData->keywords;
            $aProductDetail['extendedorder_required'] = $productData->extendedorder_required;
            $aProductDetail['backupcd_available'] = $productData->backupcd_available;

            $aProductDetail['video'] = $this->addVideo($productData);
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
    protected function getXML($url) {
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

        if (curl_errno($curl)) {
            Mage::log("Curl error" . curl_error($curl).'url: '.$url, null, $this->_logFileName);
            return 'Curl error';
        }

        // Close request to clear up some resources
        curl_close($curl);

        return $responce;
    }

    protected function getIsInStock($availability) {
        $codes = array("N_A", "OOS", "PRE_SELL", "PRE_SELL_ONLY", "PRE_SELL_BONUS", "DELISTED");

        if (in_array($availability, $codes)) {
            return false;
        }
        return true;
    }

    protected function addVideo($productData) {

        if ($videoType = $productData->videos->video->type) {

            $video = $productData->productversionid . '.' . $videoType;

            //$url  = 'http://cdn.download04.de/videos/P03214/P03214-01_Emergency_2012_Deluxe_NEU_Trailer__Trailer_462b6_Website_h264_240p.flv';

            $url = $productData->videos->video->filename;

            $path = Mage::getBaseDir('media') . DS . 'video' . DS;

            if (!file_exists($path)) {
                mkdir($path);
            }

            $data = $this->getXML($url);

            if ($data) {
                file_put_contents($path . $video, $data);
                return $video;
            }
        }
        return;
    }

    protected function addImages($productId, $data) {

        try {

            // Remove unset images, add image to gallery if exists
            $importDir1 = Mage::getBaseDir('media') . DS . 'import' . DS;

            if (!file_exists($importDir1)) {
                mkdir($importDir1);
            }
            $importDir2 = Mage::getBaseDir('media') . DS . 'import' . DS . 'tmp' . DS;

            if (!file_exists($importDir2)) {
                mkdir($importDir2);
            }
            $importDir3 = Mage::getBaseDir('media') . DS . 'import' . DS . 'tmp' . DS . $data['productversionid'] . DS;

            if (!file_exists($importDir3)) {
                mkdir($importDir3);
            }
            
            $hasMainImage = false;
            if ($data['boxshot3d_large'] != "") {
                
                $imgData3d = $this->getXML($data['boxshot3d_large']);
                
                if (!strpos($imgData3d, 'error')) {

                    $imgPath = $importDir3 . $data['productversionid'] . '_3d.png';
                    file_put_contents($imgPath, $imgData3d);
                    $hasMainImage = true;
                }
            } elseif ($data['boxshot2d_large'] != "") {                

                $imgData2d = $this->getXML($data['boxshot2d_large']);
                                          
                if (!strpos($imgData2d, 'error')) {

                    $imgPath = $importDir3 . $data['productversionid'] . '_2d.png';
                    file_put_contents($imgPath, $imgData2d);
                    $hasMainImage = true;
                }
            } 

            $aImgPathScreenShot = array();

            for ($i = 1; $i <= $data['screenshot_total']; $i++) {

                $imgData = $this->getXML($data['screenshot_' . $i]);

                if (!strpos($imgData, 'error')) {
                    $sImgFile = $importDir3 . $data['productversionid'] . '_s' . $i . '.png';
                    file_put_contents($sImgFile, $imgData);
                    $aImgPathScreenShot[] = $sImgFile;
                }
            }

            $product = Mage::getModel('catalog/product')->load($productId);

            if ($hasMainImage) {

                // Add three image sizes to media gallery
                $mediaArray = array(
                    'thumbnail'     => $imgPath,
                    'small_image'   => $imgPath,
                    'image'         => $imgPath,
                );


                foreach ($mediaArray as $imageType => $fileName) {
                    if (file_exists($fileName)) {
                        try {
                            $product->addImageToMediaGallery($fileName, $imageType, false);
                        } catch (Exception $e) {
                            echo $e->getMessage();
                        }
                    } else {
                        Mage::log("Product does not have an image or the path is incorrect. Path was:" . $fileName, null, $this->_logFileName);
                    }
                }
            }
            
            //add screenshots as additional images
            foreach ($aImgPathScreenShot as $imgPathScreenShot) {
                if (file_exists($imgPathScreenShot)) {
                    try {
                        $product->addImageToMediaGallery($imgPathScreenShot);
                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }
                } else {
                    Mage::log("Product does not have an image or the path is incorrect. Path was:" . $imgPathScreenShot, null, $this->_logFileName);
                }
            }

            $product->save();
            $this->deleteDir($importDir3);
        } catch (Exception $e) { // sku already used                    
            Mage::log("Images not able to imported for product " . $data['name'] . ": " . $e->getMessage(), null, $this->_logFileName);
        }
    }

    protected function deleteDir($dirPath) {

        if (!is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }

        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }

        $files = glob($dirPath . '*', GLOB_MARK);

        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }

        rmdir($dirPath);
    }

    protected function deactivateCategories() {

        $collection = Mage::getModel('catalog/category')->getCategories($this->_gameCategoryId, 0, false, true);

        foreach ($collection as $category) {

            if ($category->getProductCount() == 0) {
                
                $category->setData('is_active', 0);
                $category->save();
                Mage::log("Category is deactivated: ".$category->getName(), null, $this->_logFileName);

            }
        }
        return;
    }

}


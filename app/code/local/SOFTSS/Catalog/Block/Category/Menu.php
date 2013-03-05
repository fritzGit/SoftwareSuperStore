
<?php

/*
 * Copyright 2013 pcfritz.de Onlinestore GmbH – http://www.pcfritz.de
 * all rights reserved
 */

/**
 * Catalog Category Menu
 *
 * @encoding    UTF-8
 * @package     SOFTSS
 * @subpackage  SOFTSS_Catalog
 * @author      Nikolas Koumarianos <n.entwickler@pcfritz.de>
 */

class SOFTSS_Catalog_Block_Category_Menu extends Mage_Core_Block_Template {

    public function getCategories()
    {
        $rootCategoryId = Mage::app()->getStore()->getRootCategoryId();
        $categories = Mage::getModel('catalog/category')->getCategories($rootCategoryId, 0, true, true, true);

        return $categories;
    }

}

<?php

// cms pages creation
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
 
$cmsPageAboutUs = array(
            'title' => 'About Us',
            'content_heading' => 'About Us',
            'identifier' => 'about-us',
            'content' => 'About Us',            
            'is_active' => 1,
            'sort_order' => 0,
            'stores' => array(0),
            'root_template' => 'one_column'
            );
             
Mage::getModel('cms/page')->setData($cmsPageAboutUs)->save();

$cmsPageDeliveryInformation = array(
            'title' => 'Delivery Information',
            'content_heading' => 'Delivery Information',
            'identifier' => 'delivery-information',
            'content' => 'Delivery Information',            
            'is_active' => 1,
            'sort_order' => 0,
            'stores' => array(0),
            'root_template' => 'one_column'
            );
             
Mage::getModel('cms/page')->setData($cmsPageDeliveryInformation)->save();

$cmsPagePrivacyPolicy = array(
            'title' => 'Privacy Policy',
            'content_heading' => 'Privacy Policy',
            'identifier' => 'privacy-policy',
            'content' => 'Privacy Policy',            
            'is_active' => 1,
            'sort_order' => 0,
            'stores' => array(0),
            'root_template' => 'one_column'
            );
             
Mage::getModel('cms/page')->setData($cmsPagePrivacyPolicy)->save();

$cmsPageTermsConditions = array(
            'title' => 'Terms & Conditions',
            'content_heading' => 'Terms & Conditions',
            'identifier' => 'terms-conditions',
            'content' => 'Terms & Conditions',            
            'is_active' => 1,
            'sort_order' => 0,
            'stores' => array(0),
            'root_template' => 'one_column'
            );
             
Mage::getModel('cms/page')->setData($cmsPageTermsConditions)->save();

$cmsPageContactUs = array(
            'title' => 'Contact Us',
            'content_heading' => 'Contact Us',
            'identifier' => 'contact-us',
            'content' => 'Contact Us',            
            'is_active' => 1,
            'sort_order' => 0,
            'stores' => array(0),
            'root_template' => 'one_column'
            );
             
Mage::getModel('cms/page')->setData($cmsPageContactUs)->save();

$cmsPageReturns = array(
            'title' => 'Returns',
            'content_heading' => 'Returns',
            'identifier' => 'returns',
            'content' => 'Returns',            
            'is_active' => 1,
            'sort_order' => 0,
            'stores' => array(0),
            'root_template' => 'one_column'
            );
             
Mage::getModel('cms/page')->setData($cmsPageReturns)->save();

$cmsPageSiteMap = array(
            'title' => 'Site Map',
            'content_heading' => 'Site Map',
            'identifier' => 'site-map',
            'content' => 'Site Map',            
            'is_active' => 1,
            'sort_order' => 0,
            'stores' => array(0),
            'root_template' => 'one_column'
            );
             
Mage::getModel('cms/page')->setData($cmsPageSiteMap)->save();
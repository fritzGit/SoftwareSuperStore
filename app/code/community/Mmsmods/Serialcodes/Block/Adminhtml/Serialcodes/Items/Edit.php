<?php
/*
* ModifyMage Solutions (http://ModifyMage.com)
* Serial Codes - Serial Numbers, Product Codes, PINs, and More
*
* NOTICE OF LICENSE
* This source code is owned by ModifyMage Solutions and distributed for use under the
* provisions, terms, and conditions of our Commercial Software License Agreement which
* is bundled with this package in the file LICENSE.txt. This license is also available
* through the world-wide-web at this URL: http://www.modifymage.com/software-license
* If you do not have a copy of this license and are unable to obtain it through the
* world-wide-web, please email us at license@modifymage.com so we may send you a copy.
*
* @category		Mmsmods
* @package		Mmsmods_Serialcodes
* @author		David Upson
* @copyright	Copyright 2012 by ModifyMage Solutions
* @license		http://www.modifymage.com/software-license
*/
 
class Mmsmods_Serialcodes_Block_Adminhtml_Serialcodes_Items_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
               
        $this->_objectId = 'id';
        $this->_blockGroup = 'serialcodes';
        $this->_controller = 'adminhtml_serialcodes_items';
 
        $this->_updateButton('save', 'label', Mage::helper('serialcodes')->__('Save Codes'));
		$this->_removeButton('delete');
    }
 
    public function getHeaderText()
    {
        return Mage::helper('serialcodes')->__('Edit Order Item Serial Code(s)');
    }
	
	public function getHeaderHtml()
    {
		$html = parent::getHeaderHtml();
		
		return $html;
    }

}
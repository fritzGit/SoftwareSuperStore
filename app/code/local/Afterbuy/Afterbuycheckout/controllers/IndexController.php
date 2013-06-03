<?php
class Afterbuy_Afterbuycheckout_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/afterbuycheckout?id=15 
    	 *  or
    	 * http://site.com/afterbuycheckout/id/15 	
    	 */
    	/* 
		$afterbuycheckout_id = $this->getRequest()->getParam('id');

  		if($afterbuycheckout_id != null && $afterbuycheckout_id != '')	{
			$afterbuycheckout = Mage::getModel('afterbuycheckout/afterbuycheckout')->load($afterbuycheckout_id)->getData();
		} else {
			$afterbuycheckout = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	
    	if($afterbuycheckout == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$afterbuycheckoutTable = $resource->getTableName('afterbuycheckout');
			
			$select = $read->select()
			   ->from($afterbuycheckoutTable,array('afterbuycheckout_id','user_name','partner_id','partner_pass','status','feedback','artikelerkennung','kundenerkennung','check_doppelbestellung'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$afterbuycheckout = $read->fetchRow($select);
		}
		Mage::register('afterbuycheckout', $afterbuycheckout);
		

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}
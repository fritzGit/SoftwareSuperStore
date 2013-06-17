<?php
class SOFTSS_Softdistribution_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/softdistribution?id=15 
    	 *  or
    	 * http://site.com/softdistribution/id/15 	
    	 */
    	/* 
		$softdistribution_id = $this->getRequest()->getParam('id');

  		if($softdistribution_id != null && $softdistribution_id != '')	{
			$softdistribution = Mage::getModel('softdistribution/softdistribution')->load($softdistribution_id)->getData();
		} else {
			$softdistribution = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($softdistribution == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$softdistributionTable = $resource->getTableName('softdistribution');
			
			$select = $read->select()
			   ->from($softdistributionTable,array('softdistribution_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$softdistribution = $read->fetchRow($select);
		}
		Mage::register('softdistribution', $softdistribution);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}
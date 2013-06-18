<?php
class SOFTSS_Softd_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/softd?id=15 
    	 *  or
    	 * http://site.com/softd/id/15 	
    	 */
    	/* 
		$softd_id = $this->getRequest()->getParam('id');

  		if($softd_id != null && $softd_id != '')	{
			$softd = Mage::getModel('softd/softd')->load($softd_id)->getData();
		} else {
			$softd = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($softd == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$softdTable = $resource->getTableName('softd');
			
			$select = $read->select()
			   ->from($softdTable,array('softd_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$softd = $read->fetchRow($select);
		}
		Mage::register('softd', $softd);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}
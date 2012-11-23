<?php

class Zend_View_Helper_Lastarticles extends Zend_View_Helper_Abstract{
	
	
   /**
	 * Kthen $limit artikujt e fundit
	 * 
	 */
    public function lastarticles($limit){	
    	$artObj = new App_Model_DbTable_cmsartikujt();
    	return $artObj->getLastArts($limit);    
	}
}
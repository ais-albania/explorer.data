<?php

class Zend_View_Helper_Lastupdate extends Zend_View_Helper_Abstract
{
    public function lastupdate()
	{
    	$artObj = new App_Model_DbTable_cmsartikujt();
    	return $artObj->getLastUpdatedArt();    
	}
}

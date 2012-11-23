<?php
/**
 * Helper qe kthen nje link nga text ne link te klikueshem
 *
 */
class Zend_View_Helper_CanonicalUrl extends Zend_View_Helper_Abstract
{
	function canonicalUrl(){
		$request = Zend_Controller_Front::getInstance()->getRequest();
		
		$filter = new Zend_Filter_Alnum(true);
		$params = array();
		$params[]=$request->getControllerName();
		$params[]=$request->getActionName();
		
		foreach($request->getParams() as $key => $value) {
			if(in_array($key, array('controller', 'action', 'module'))) {
				continue;
			}
			if ($key!="titulli")
				array_push($params,  $filter->filter($key) . '/' . $filter->filter($value));
		}	

		return implode('/', $params);
	}
}
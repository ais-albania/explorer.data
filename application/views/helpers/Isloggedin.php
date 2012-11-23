<?php
class Zend_View_Helper_Isloggedin extends Zend_View_Helper_Abstract
{ 
    
   /**
	 * Kthen true nesa ka sesion ose cookie
	 * 
	 */
	public function _Isloggedin() {
		//Ruaj Sessionin
		$perdSession = new Zend_Session_Namespace ( 'PerdoruesSess' );
		if (isset ( $perdSession->username )) {
			return true;
		}
		else if (isset ( $_COOKIE ["opendatauser"] )) {
			$cookiecrypt = new App_Crypt ();
			$perdSession->username = $cookiecrypt->decrypt ( $_COOKIE ["opendatauser"] );
			return true;
		}
		return false;
	}
    
    

    
}




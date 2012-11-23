<?php
/**
 * Helper qe kthen nje link nga text ne link te klikueshem
 *
 */
class Zend_View_Helper_Language extends Zend_View_Helper_Abstract
{
	function language(){
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$lang = $request->getParam('lang');
		$url=addslashes(htmlspecialchars(strip_tags($_SERVER["REQUEST_URI"])));
		
		//Del vetem njeri flamur, ose shqip ose anglisht
		if ($lang == "en") {
			$othergj='sq';
			$url = str_replace ( "en", "sq", $url );
			//Rasti i indeksit kur nuk ka asnje variabel ne URL
			if ($url == "" || $url=='/') {$url = '/'.$othergj;}
			$text='<a href="'.$url.'"><img src="/images/lang_al.png" alt="Shqip" title="Shqip" width="16" height="12"/></a>';
		} else {
			$othergj='en';
			$url = str_replace ( "sq", "en", $url );
			//Rasti i indeksit kur nuk ka asnje variabel ne URL			
			if ($url == "" || $url=='/') {$url = '/'.$othergj;}
			$text='<a href="'.$url.'"><img src="/images/lang_en.png" alt="English" title="English" width="16" height="12"/></a>';
		}

		$content = '<div id="flag">'.$text.'</div>';
	return $content; 
	}
}
<?php

class Zend_View_Helper_Attachedfiles extends Zend_View_Helper_Abstract
{
    /**
     * 
     * Vide helper qe kthen skedaret
     * @param array $files
     * @param string $class
     */
	public function attachedfiles ($files,$class="files"){
		$txt= '<ul class="$class">';
		foreach ($files as $file){
			$filesked=pathinfo($file["SkedarPath_gj1"]);
			$txt.='<li><a href="/uploadserise/skedaret/'.urlencode($file["SkedarPath_gj1"]).'"><img src="/assetserise/icons/'.$filesked["extension"].'.gif" alt="'.$file["SkedarTitull_gj1"].'" > '.$file["SkedarTitull_gj1"].'</a></li>';	
		}
		$txt.='</ul>';
    	return $txt;
    	
    }
}
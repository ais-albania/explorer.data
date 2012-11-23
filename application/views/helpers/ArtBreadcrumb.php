<?php

class Zend_View_Helper_ArtBreadcrumb extends Zend_View_Helper_Abstract
{
    public function artBreadcrumb ($katPrindID){
    	$bcrumb='<a href="/">Kreu</a>';
    	$kats=new App_Model_DbTable_cmskategori();
    	//$bcrumb.=$kats->generateMenu($katPrindID);
    	$arr=$kats->generateKatPrind($katPrindID);
    	$emra=array_reverse($arr["emer"]);
    	$id=array_reverse($arr["ID"]);
    	   	
//    	print_r ($arr);
$i=0;
    	foreach ($emra as $kats){    		
    		$bcrumb.=' &raquo; <a href="/lajme/kat/knr/'.$id[$i].'/'.$kats.'">'.$kats.'</a>';
    		$i++;	
    	}    	
    	// <a href="#">Letersia</a> &raquo; <a href="#">Rilindja</a> &raquo; <a href="#">Naim Frasheri</a>
    	return $bcrumb;
    	
    }
}
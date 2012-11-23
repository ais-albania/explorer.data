<?php
class Zend_View_Helper_Menu extends Zend_View_Helper_Abstract 
{	
	/**
	 * Duhet rishkruar per permiresim
	 * Enter description here ...
	 * @param int $spec
	 */
	public function Menu($spec=0)
	{
		$menu=$this->menuArray("KatSpecial=$spec");
		if ($spec==0){			
			echo '<ul class="topnav">';
			foreach($menu as &$kats) : 
			if (is_array($kats["submenu"]))
				$link = "";
			else 
			    $link = $this->CleanUrl($kats["link"]);
			?>
					<li>
				    	<a href="<?=$link;?>" title="<?php echo $kats["title"];?>"><?php echo $kats["title"];?></a>
				    		<?php if (is_array($kats["submenu"])) 
				    		{?>
					    		<ul class="subnav">
								<?php foreach($kats["submenu"] as &$subkats) : ?>
		            			<li><a href="<?php echo $this->CleanUrl($subkats["link"]);?>"><?php echo $subkats["title"];?></a></li>
								<?php endforeach; 
								echo '</ul>';
				    		}
							?>
					</li>
			<?php endforeach;
			echo '</ul>';
		}
		else{
			//Menuja speciale vjen ketu (zakonisht menuja qe vihet ne header
			//menuja me linqe siper fare
			$menutxt='<ul>';
			foreach ($menu as $kats){
				$menutxt.= '<li><a href="'.$this->CleanUrl($kats["link"]).'" />'.$kats["title"].'</a></li>';
			}
			$menutxt.='</ul>';
			echo $menutxt;
		} 
	}
	/**
	 * Funksion qe merr nje strukture Prind-Femije Zakonisht Menu dhe e kthen ate ne nje vektor multidimensional
	 * nxjerr menute ne nje vektor te formes menu["title"], $menu["link"],$menu["submenu"]
	 * 
	 * @param string $catParent
	 * @param string $catSpec
	 * @param string $otherCondition
	 * @param string $orderBy
	 * @param string $gj
	 * @return array[][]
	 *@author Armand Brahaj
	 * @example 
			$menumajtas=menuArray(" IS NULL","1","","KatRend",$gj);
			foreach ($menumajtas as $menuelement)
			{
				echo $menuelement["title"];
			    if (is_array($menuelement["submenu"]))
			    {
			    	foreach ($menuelement["submenu"] as $menuSubElement)
					{
			        	echo '<a href="'.$menuSubElement["link"].'">'.$menuSubElement["title"].'</a>';
			        }
			    }
			}
			unset($menumajtas);
			unset($menuelement);
			unset($menuSubElement);
			//i fshij qe te mos me ngaterrohen me ndonje deklarim tjeter te ketyre elementeve
	 */
	function menuArray($catSpec = "KatSpecial=0", $catParent = "KatPrindID = 0",  $orderBy = "KatRend ASC", $gj = "gj1") {	
		$menuObj = new App_Model_DbTable_cmskategori();
		$kats=$menuObj->fetchAll(
			array($catParent,$catSpec),
			$orderBy,
			12,0);
		

		$menu = array();
		$i = - 1;
		foreach ($kats as $kat ) {
			$i ++;
			$menu [$i] ["title"] = $kat ["KatEmer_" . $gj];
			if ($kat ["KatLloji"] == "KAT_LINK_TYPE_AUTO") {
				$menu [$i] ["link"] = '/lajme/kat/kid/' . $kat ["KatID"].'/titulli/'.$this->CleanUrl($kat ["KatEmer_" . $gj]);
			} elseif ($kat ["KatLloji"] == "KAT_LINK_TYPE_NONE") {
				$menu [$i] ["link"] = "";
			} elseif ($kat ["KatLloji"] == "KAT_LINK_TYPE_PREPROGRAMMED") {
				$menu [$i] ["link"] = '?fq=' . $kat ["KatFqInclude"];
			} elseif ($kat ["KatLloji"] == "KAT_LINK_TYPE_URL") {
				$menu [$i] ["link"] = $kat ["KatFqInclude"];
			}
			$kid=$kat["KatID"];
			$nr=count($menuObj->getSubCatByCatID($kid));

			//$nr=$resBij; 
			if ( $nr > 0 ){
				$menu [$i] ["submenu"] = $this->menuArray ($catSpec,"KatPrindID=$kat[KatID]",$orderBy,$gj);
			}
	//if second menu, check here!
//print_r ($kat);
		} //end foreach
		return $menu;
	}
		function CleanUrl($toclean) {
		$toclean = ereg_replace ( "ë", "e", $toclean );
		$toclean = ereg_replace ( " ", "-", $toclean );
        $toclean = ereg_replace("ç","c",$toclean);        
		$toclean = ereg_replace ( "\"", "", $toclean );
		$toclean = ereg_replace ( "\?", "", $toclean );		
		return $toclean;	
	}	
}

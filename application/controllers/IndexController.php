<?php

class IndexController extends Zend_Controller_Action
{

    public function init(){
        //ini_set('display_errors',1);
    }
    

    public function indexAction(){
        $bootstrap = $this->getInvokeArg ( 'bootstrap' );
		$options = $bootstrap->getOptions ();
		$cache = $bootstrap->getResource ( 'cache' );

		if (! $topics = $cache->load ( 'topics' )) {
		    $topics=$this->retrieveTopics();
			$cache->save ( $topics, 'topics' );
		}
		
        $this->view->topics = $topics;
    }
    

    /**
     * Retrieve indicators of a speficic topic in ajax request
     * marilda: vetem indikatore qe kane nje skedare bashkangjitur shfaqen
     * TODO: Subindicators?
     */
    public function indicatorAction(){
        $this->_helper->layout()->disableLayout();
        $bootstrap = $this->getInvokeArg ( 'bootstrap' );
		$options = $bootstrap->getOptions ();
		$cache = $bootstrap->getResource ( 'cache' );

        $filterChain = new Zend_Filter ( );
		$filterChain->addFilter ( new Zend_Filter_Alnum ( ) );
		$topic = $filterChain->filter ( $this->_getParam ( 'topic' ) );
        $this->view->topic = $topic;
        
		$topicname="Indicatorsfor".$topic;
        if (! $indicators = $cache->load ( $topicname )) {
		    $indicators=$this->retrieveIndicator($topic);
			$cache->save ( $indicators, $topicname );
		}
		
		$ind_arr = $this->getIndFiles();
		/**
		$indicator = array();
		$nofiles = 0;
		foreach ($indicators as $ind) :
		   if (in_array($ind, $ind_arr)) {		   
		      $indicator[$nofiles]= $ind;   
		      $nofiles++; 
		   }	
		endforeach; 
		**/
		$this->view->indicator=$indicators;
    }

    /**
     * Retrieve years that have data for a specific indicator
     * Enter description here ...
     * @param string $indicator
     */
    public function yearsAction(){
        $this->_helper->layout()->disableLayout();
        $bootstrap = $this->getInvokeArg ( 'bootstrap' );
		$options = $bootstrap->getOptions ();
		$cache = $bootstrap->getResource ( 'cache' );

        $filterChain = new Zend_Filter ( );
		$filterChain->addFilter ( new Zend_Filter_Alnum ( ) );
		$indicator = $filterChain->filter ( $this->_getParam ( 'indicator' ) );

		$yearsForIndicator="YearsFor".$indicator;		
        if (! $years = $cache->load ( $yearsForIndicator )) {
		    $years=$this->retrieveYearsForIndicator($indicator);
			$cache->save ( $years, $yearsForIndicator );
		}
		sort($years);
		$this->view->years=$years;
		$this->view->indicator=$indicator;
    }
    
    
    
   /**
     * Kthen te dhenat sipas kerkimit
     * Shfaq grafik
     * 
     */
    public function retrievedataAction(){
        $filterChain = new Zend_Filter ( );
		$filterChain->addFilter ( new Zend_Filter_Alnum ( ) );
		$indicator = $filterChain->filter ( $this->_getParam ( 'indicator' ) );		
		$years = $this->_getParam ( 'years' ) ;			
		$years_arr = explode(",", $years);		
		$countNo = count($years_arr);		
		$viti = $years_arr[0] ;     
		if ($this->hasProduct($indicator)){
			$this->view->withProduct = true;
			//echo "ka produkt";
			//kur ka produkte, ka vetem nje shtet
			//produktet do te sherbejne si indikatore per motion chart (parametri 1)		   
		    $query="PREFIX  rdf:  <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                    PREFIX  rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                    PREFIX  oda:  <http://open.data.al/oda2.owl#>
                    PREFIX  sdmx-measure:  <http://purl.org/linked-data/sdmx/2009/measure#>

                    SELECT  ?indicator ?year ?value ?country ?product
                    FROM <http://open.data.al/semanticfiles/xml/".$indicator.".xml>
                    WHERE {
                      ?x rdf:type oda:DataEntry .
                      ?x oda:year ?year . 
                      ?x oda:indicator ?indicator .
                      ?x oda:country ?country .
                      ?x oda:product ?product .
                      ?x sdmx-measure:obsValue ?value.
                      ?x oda:indicator <http://open.data.al/oda2.owl#".$indicator.">.
                      FILTER ( sameTerm(?year, <http://dbpedia.org/resource/".$viti.">) ";
		              for ($i = 1; $i < $countNo; $i++) {
                          	$viti = $years_arr[$i] ;                   	                    	
                            $query .= "|| sameTerm(?year, <http://dbpedia.org/resource/".$viti.">) ";
 		              }    
                   $query .= " ) }";       
		}
		else{
			//nuk ka produkte, ka nje ose me shume shtete
			//shtetet do te sherbejne si indikatore per motion chart(parametri 1)
			//echo "nuk ka produkte";
			$this->view->withProduct = false;
			echo $this->hasUniqueCountry($indicator);
		    $query="PREFIX  rdf:  <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                    PREFIX  rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                    PREFIX  oda:  <http://open.data.al/oda2.owl#>
                    PREFIX  sdmx-measure:  <http://purl.org/linked-data/sdmx/2009/measure#>

                    SELECT  ?indicator ?year ?value ?country
                    FROM <http://open.data.al/semanticfiles/xml/".$indicator.".xml>
                    WHERE {
                      ?x rdf:type oda:DataEntry .
                      ?x oda:year ?year . 
                      ?x oda:indicator ?indicator .
                      ?x oda:country ?country .
                      ?x sdmx-measure:obsValue ?value.
                      ?x oda:indicator <http://open.data.al/oda2.owl#".$indicator.">.
                      FILTER ( sameTerm(?year, <http://dbpedia.org/resource/".$viti.">) ";
		              for ($i = 1; $i < $countNo; $i++) {
                          	$viti = $years_arr[$i] ;                   	                    	
                            $query .= "|| sameTerm(?year, <http://dbpedia.org/resource/".$viti.">) ";
 		              }    
                   $query .= " ) }";  
                   
                   
		}
        $url="http://www.qcrumb.com/sparql?query=".urlencode($query)."&rules=&accept=";
        $result = $this->retrieveContent($url);
        $this->view->metadata = $this->retrieveMetaDataIndicator($indicator);
        $this->view->data = serialize($this->parseresultasArray($result, $this->hasUniqueCountry($indicator)));
    }
    
    
    
   /**
     * Kthen te dhenat sipas kerkimi
     * Perfshirja e viteve - OK
     * 
     */
     public function retrievedataajaxAction(){
        $this->_helper->layout()->disableLayout();                
        $filterChain = new Zend_Filter ( );
		$filterChain->addFilter ( new Zend_Filter_Alnum ( ) );
		$indicator = $filterChain->filter ( $this->_getParam ( 'indicator' ) );
		$this->view->indicator = $indicator; 	
		$years = $this->_getParam ( 'years' ) ;	
		$years1 = trim($years,",") ;
		$this->view->years = $years; 			
		$years_arr = explode(",", $years);	
		$countNo = count($years_arr);		
		$viti = $years_arr[0] ;                
		if ($this->hasProduct($indicator)){	
			//print_r ("ka produkt");			
		    $query="PREFIX  rdf:  <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                    PREFIX  rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                    PREFIX  oda:  <http://open.data.al/oda2.owl#>
                    PREFIX  sdmx-measure:  <http://purl.org/linked-data/sdmx/2009/measure#>

                    SELECT  ?indicator ?year ?value ?country ?product
                    FROM <http://open.data.al/semanticfiles/xml/".$indicator.".xml>
                    WHERE {
                      ?x rdf:type oda:DataEntry .
                      ?x oda:year ?year . 
                      ?x oda:indicator ?indicator .
                      ?x oda:country ?country .
                      ?x oda:product ?product .
                      ?x sdmx-measure:obsValue ?value.
                      ?x oda:indicator <http://open.data.al/oda2.owl#".$indicator.">.
                      FILTER ( sameTerm(?year, <http://dbpedia.org/resource/".$viti.">) ";
		              for ($i = 1; $i < $countNo; $i++) {
                          	$viti = $years_arr[$i] ;                   	                    	
                            $query .= "|| sameTerm(?year, <http://dbpedia.org/resource/".$viti.">) ";
 		              }    
                   $query .= " ) }";                 	
                  
		}
		else{	
			//print_r ("nuk ka produkt");		
			if ($this->hasUniqueCountry($indicator)) echo "UNIK"; else echo "JO unik";		
		    $query="PREFIX  rdf:  <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                    PREFIX  rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                    PREFIX  oda:  <http://open.data.al/oda2.owl#>
                    PREFIX  sdmx-measure:  <http://purl.org/linked-data/sdmx/2009/measure#>

                    SELECT  ?indicator ?year ?value ?country
                    FROM <http://open.data.al/semanticfiles/xml/".$indicator.".xml>
                    WHERE {
                      ?x rdf:type oda:DataEntry .
                      ?x oda:year ?year .
                      ?x oda:indicator ?indicator .
                      ?x oda:country ?country .
                      ?x sdmx-measure:obsValue ?value.
                      ?x oda:indicator <http://open.data.al/oda2.owl#".$indicator.">.
                      FILTER ( sameTerm(?year, <http://dbpedia.org/resource/".$viti.">) ";
		              for ($i = 1; $i < $countNo; $i++) {
                          	$viti = $years_arr[$i] ;                   	                    	
                            $query .= "|| sameTerm(?year, <http://dbpedia.org/resource/".$viti.">) ";
 		              }    
                   $query .= " ) }";  
                   
		}
        $url="http://www.qcrumb.com/sparql?query=".urlencode($query)."&rules=&accept=";
        $this->view->metadata = $this->retrieveMetaDataIndicator($indicator);
			//	$queryResult="ResultsFor".$indicator.$years1;
		//echo $queryResult;
       // if (! $result = $cache->load ( $queryResult )) {
		 //   $result = $this->retrieveContent($url);        
		//	$cache->save ( $result, $queryResult );
		//}
		//echo "Unik".$this->hasUniqueCountry($indicator);
		
        $result = $this->retrieveContent($url);     
           
        $this->view->data = serialize($this->parseresultasArray($result, $this->hasUniqueCountry($indicator)));
    }
    
    
    
   /**
     * Shfaq te dhena tabelare me Spark
     * 
     * 
     */
     public function sparktableajaxAction(){
        $this->_helper->layout()->disableLayout();
        $filterChain = new Zend_Filter ( );
		$filterChain->addFilter ( new Zend_Filter_Alnum ( ) );
		$indicator = $filterChain->filter ( $this->_getParam ( 'indicator' ) );
		$this->view->indicator = $indicator;
		//$years = $filterChain->filter ( $this->_getParam ( 'years' ) );
		if ($this->hasProduct($indicator)){
		    $query="PREFIX  rdf:  <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                    PREFIX  rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                    PREFIX  oda:  <http://open.data.al/oda2.owl#>
                    PREFIX  sdmx-measure:  <http://purl.org/linked-data/sdmx/2009/measure#> 
                    SELECT  ?x ?product ?year ?country   ?Vlera
                    FROM <http://open.data.al/semanticfiles/xml/".$indicator.".xml>
                    WHERE {
                      ?x rdf:type oda:DataEntry .
                      ?x oda:year ?year .
                      ?x oda:indicator ?indicator .                      
                      ?x oda:country ?country .                      
                      ?x oda:product ?product .                                          
                      ?x sdmx-measure:obsValue ?Vlera.
                      ?x oda:indicator <http://open.data.al/oda2.owl#".$indicator.">
                    }                    
                    ORDER BY ?product ?year ?country
                    
                    ";
		}else{
		    $query="PREFIX  rdf:  <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                    PREFIX  rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                    PREFIX  oda:  <http://open.data.al/oda2.owl#>
                    PREFIX  sdmx-measure:  <http://purl.org/linked-data/sdmx/2009/measure#>

                    SELECT  ?x  ?year ?country ?Vlera
                    FROM <http://open.data.al/semanticfiles/xml/".$indicator.".xml>
                    WHERE {
                      ?x rdf:type oda:DataEntry .
                      ?x oda:year ?year .
                      ?x oda:indicator ?indicator .                      
                      ?x oda:country ?country .
                      ?x sdmx-measure:obsValue ?Vlera.
                      ?x oda:indicator <http://open.data.al/oda2.owl#".$indicator.">
                    }
                    ORDER BY  ?year ?country";
		}
        $this->view->query = $query;
     }
     
     
   /**
     * Shfaq te pie chart me Spark
     * pa mbaruar
     * 
     */
     public function sparkpiechartajaxAction(){
        $this->_helper->layout()->disableLayout();

        $filterChain = new Zend_Filter ( );
		$filterChain->addFilter ( new Zend_Filter_Alnum ( ) );
		$indicator = $filterChain->filter ( $this->_getParam ( 'indicator' ) );
		$this->view->indicator = $indicator;
		//$years = $filterChain->filter ( $this->_getParam ( 'years' ) );
		if ($this->hasProduct($indicator)){
		    $query="PREFIX  rdf:  <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                    PREFIX  rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                    PREFIX  oda:  <http://open.data.al/oda2.owl#>
                    PREFIX  sdmx-measure:  <http://purl.org/linked-data/sdmx/2009/measure#> 
                    SELECT  ?x ?product ?year ?countrylbl  ?Vlera
                    FROM <http://open.data.al/semanticfiles/xml/".$indicator.".xml>
                    WHERE {
                      ?x rdf:type oda:DataEntry .
                      ?x oda:year ?year .
                      ?x oda:indicator ?indicator .                      
                      ?x oda:country ?country .
                      ?country rdfs:label ?countrylbl .
                      ?x oda:product ?product .                                          
                      ?x sdmx-measure:obsValue ?Vlera.
                      ?x oda:indicator <http://open.data.al/oda2.owl#".$indicator.">
                    }
                    ORDER BY ?product ?year ?country
                    ";
		}else{
		    $query="PREFIX  rdf:  <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                    PREFIX  rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                    PREFIX  oda:  <http://open.data.al/oda2.owl#>
                    PREFIX  sdmx-measure:  <http://purl.org/linked-data/sdmx/2009/measure#>

                    SELECT  ?x  ?year ?country ?Vlera
                    FROM <http://open.data.al/semanticfiles/xml/".$indicator.".xml>
                    WHERE {
                      ?x rdf:type oda:DataEntry .
                      ?x oda:year ?year .
                      ?x oda:indicator ?indicator .                      
                      ?x oda:country ?country .
                      ?x sdmx-measure:obsValue ?Vlera.
                      ?x oda:indicator <http://open.data.al/oda2.owl#".$indicator.">
                    }
                    ORDER BY  ?year ?country";
		}
        $this->view->query = $query;
     }
     

    private function parseresultasArray($text,$hasUniqueCountry){ 
        //splitting lines in arrays
        $matches = preg_split("/((\r(?!\n))|((?<!\r)\n)|(\r\n))/", $text);
        $arr = array();
        foreach ($matches as $element):
            $vlera = explode("|", $element);
            //preg_match_all("/<(.*)>/",$element,$found);
            preg_match_all("|<http://open.data.al/oda2.owl#(.*)>|U",$vlera[1],$indicator);
            preg_match_all("|<http://dbpedia.org/resource/(.*)>|U",$vlera[2],$year);
            preg_match_all('|"(.*)"|',$vlera[3],$value);
            preg_match_all("|<http://dbpedia.org/resource/(.*)>|U",$vlera[4],$country);
            //shtoj veteme rekordet me te gjitha vlerat 
            if ($indicator[1][0]!=""){
                if ($this->hasProduct($indicator[1][0])){
                    $elementarr= array(
                        "indicator"=>$indicator[1][0],
                        "country"=> $country[1][0],
                        "viti" =>$year[1][0],
                        "value"=>$value[1][0],
                        "product"=>trim($vlera[5],""),
                    );
                }
                else {
	                 if ($hasUniqueCountry){
	                    $elementarr= array(	
	                        "indicator"=>$indicator[1][0],
	                        "country"=>$country[1][0],                       
	                        "viti" =>$year[1][0],
	                        "value"=>$value[1][0]
	                       
	                    );
	                 }
	                 else{
	                    $elementarr= array(
	                        "indicator"=>$indicator[1][0],
	                        "country"=>$country[1][0],
	                        "viti" =>$year[1][0],
	                        "value"=>$value[1][0]
	                    );
                    }
               }
            $arr[]=$elementarr;
            //print_r($elementarr);
            }           
        endforeach;

        return $arr;
    }
    /**
     *
     * Check if this has a product or not
     * @param string $indicator
     */
    private function hasProduct($indicator){
        $query="PREFIX  rdf:  <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
            PREFIX  rdfs: <http://www.w3.org/2000/01/rdf-schema#>
            PREFIX  oda:  <http://open.data.al/oda2.owl#>
            PREFIX  sdmx-measure:  <http://purl.org/linked-data/sdmx/2009/measure#>

            SELECT ?product
            FROM <http://open.data.al/semanticfiles/xml/".$indicator.".xml>
            WHERE {
               ?x oda:product ?product .
               ?x oda:indicator <http://open.data.al/oda2.owl#".$indicator.">
            }";
        $url="http://www.qcrumb.com/sparql?query=".urlencode($query)."&rules=&accept=";
        $result = $this->retrieveContent($url);
        preg_match_all("|\"(.*)\"|U",$result,$match);
        if (count ($match[1])>1)
            return true;
         else   
            return false;
    }

    private function hasUniqueCountry($indicator){
        $query="PREFIX  rdf:  <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
	            PREFIX  rdfs: <http://www.w3.org/2000/01/rdf-schema#>
	            PREFIX  oda:  <http://open.data.al/oda2.owl#>
	
	            SELECT  DISTINCT ?country
	            FROM <http://open.data.al/semanticfiles/xml/".$indicator.".xml>
	            WHERE {
	                ?x oda:country ?country .
	            }";
        $url="http://www.qcrumb.com/sparql?query=".urlencode($query)."&rules=&accept=";

        $bootstrap = $this->getInvokeArg ( 'bootstrap' );
		$options = $bootstrap->getOptions ();
		$cache = $bootstrap->getResource ( 'cache' );

		//$uniqueCountryForIndicator="uniqueCountry".$indicator;
       // if (! $result = $cache->load ( $uniqueCountryForIndicator )) {
		   $result = $this->retrieveContent($url);
		//$cache->save ( $result, $uniqueCountryForIndicator );
		//}        
        preg_match_all("|<http://dbpedia.org/resource/(.*)>|U",$result,$match);        
        if (count ($match[1])>1)
           return false;
         else   
            return true;
    }
    /**
     *
     * Retrieve subindicators and show them on the page
     * //TODO
     */
    public function subindicatorAction(){
    	$this->_helper->layout()->disableLayout();
        $bootstrap = $this->getInvokeArg ( 'bootstrap' );
		$options = $bootstrap->getOptions ();
		$cache = $bootstrap->getResource ( 'cache' );

        $filterChain = new Zend_Filter ( );
		$filterChain->addFilter ( new Zend_Filter_Alnum ( ) );
		$indicator = $filterChain->filter ( $this->_getParam ( 'indicator' ) );
		$SubindForIndicator="SubindicatorsFor".$indicator;		
        if (! $subindicators = $cache->load ( $SubindForIndicator )) {
		    $subindicators=$this->retrieveSubIndicator($indicator);
			$cache->save ( $subindicators, $SubindForIndicator );
		}
		sort($subindicators);
		$this->view->subindicators=$subindicators;
		$this->view->indicator=$indicator;

    }
    
    
    /**
     *
     * kthen vitet per nje indikator te dhene
     * return @array
     */
    private function retrieveYearsForIndicator($indicator){
        $query="PREFIX  rdf:  <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                PREFIX  rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                PREFIX  oda:  <http://open.data.al/oda2.owl#>
                SELECT  DISTINCT ?year
                FROM <http://open.data.al/semanticfiles/xml/".$indicator.".xml>
                WHERE {
                    ?x oda:year ?year .
                }";
        $url="http://www.qcrumb.com/sparql?query=".urlencode($query)."&rules=&accept=";
        $result = $this->retrieveContent($url);
        preg_match_all("|<http://dbpedia.org/resource/(.*)>|U",$result,$match);
        return $match[1];
    }
    
    
    /**
     *
     * Retrieve topics from the Ontology
     * return @array
     */
    private function retrieveTopics(){
        $query="PREFIX rdf:<http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                PREFIX oda:<http://open.data.al/oda2.owl#>
                SELECT ?topic
                FROM <http://open.data.al/oda03.owl#>
                WHERE {
                  ?topic rdf:type oda:Topic .
                }
                ORDER BY ASC(?topic)";
        $url="http://www.qcrumb.com/sparql?query=".urlencode($query)."&rules=&accept=";
        $result = $this->retrieveContent($url);
        preg_match_all("|<http://open.data.al/oda2.owl#(.*)>|U",$result,$match);
        return $match[1];
    }

    /**
     * Retrieve Indicators for a specific Topic
     * ndryshoj query: kap vetem indikatoret prind per nje topic te dhene
     * return @array
     */
    private function retrieveIndicator($topic){
	
        $query="PREFIX rdf:<http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                PREFIX oda:<http://open.data.al/oda2.owl#>
                SELECT ?indicator
                FROM <http://open.data.al/oda03.owl#>
                WHERE {
                  ?indicator oda:topic oda:".$topic." .
                  ?indicator rdf:type oda:Indicator .
                 }
                 ORDER BY ASC(?indicator)";                
        $url="http://www.qcrumb.com/sparql?query=".urlencode($query)."&rules=&accept=";
        $result = $this->retrieveContent($url);       
        preg_match_all("|<http://open.data.al/oda2.owl#(.*)>|U",$result,$match);
        return $match[1];
    }

    /**
     *
     * Retrieve subindicators if there are any
     * @param String $indicator
     * @return ArrayIterator
     */
    private function retrieveSubIndicator($indicator){

        $query="PREFIX rdf:<http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                PREFIX oda:<http://open.data.al/oda2.owl#>
                SELECT ?subindicator
                FROM <http://open.data.al/oda03.owl#>
                WHERE {
                  ?subindicator oda:parentIndicator oda:".$indicator." .
                  ?subindicator rdf:type oda:Indicator .
                }";
        $url="http://www.qcrumb.com/sparql?query=".urlencode($query)."&rules=&accept=";
        $result = $this->retrieveContent($url);
        preg_match_all("|<http://open.data.al/oda2.owl#(.*)>|U",$result,$match);
        //TODO check if there is some result
        return $match[1];
    }
    

    private function retrieveMetaDataIndicator($indicator){
        $query="PREFIX  dc: <http://purl.org/dc/elements/1.1/>
                SELECT  ?title ?publisher ?creator
                FROM <http://open.data.al/semanticfiles/xml/".$indicator.".xml>
                WHERE   {
                ?titull dc:title ?title .
                ?burimi dc:publisher ?publisher.
                ?krijuesi dc:creator ?creator.
                }";
       // $this->log($query);
        $bootstrap = $this->getInvokeArg ( 'bootstrap' );
		$options = $bootstrap->getOptions ();
		$cache = $bootstrap->getResource ( 'cache' );

		$metadataname="metadata".$indicator;
        if (! $metadata = $cache->load ( $metadataname )) {
            $url="http://www.qcrumb.com/sparql?query=".urlencode($query)."&rules=&accept=text/comma-separated-values";
            $result = $this->retrieveContent($url);
            $matches = preg_split("/((\r(?!\n))|((?<!\r)\n)|(\r\n))/", $result);
            $metadata=explode(',',$matches[1]);
			$cache->save ( $metadata, $metadataname );
		}

        return $metadata;
    }
    
    

    
    /**
     * Connect to a remote server via Curl and retrieve web content
     **/
    private function retrieveContent($url){
        $curl = curl_init ();

		$header [0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
		$header [0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
		$header [] = "Cache-Control: max-age=0";
		$header [] = "Connection: keep-alive";
		$header [] = "Keep-Alive: 900";
		$header [] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
		$header [] = "Accept-Language: en-us,en;q=0.5";
		$header [] = "Pragma: "; // browsers keep this blank.

		curl_setopt ( $curl, CURLOPT_URL, $url );
		curl_setopt ( $curl, CURLOPT_USERAGENT, 'Quareos/2.1 (+http://agent.quareos.com/)' );
		curl_setopt ( $curl, CURLOPT_HTTPHEADER, $header );
		curl_setopt ( $curl, CURLOPT_REFERER, 'http://www.quareos.com' );
		curl_setopt ( $curl, CURLOPT_ENCODING, 'gzip,deflate' );
		curl_setopt ( $curl, CURLOPT_AUTOREFERER, true );
		curl_setopt ( $curl, CURLOPT_FOLLOWLOCATION, TRUE );
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $curl, CURLOPT_TIMEOUT, 290 );

		$html = curl_exec ( $curl );		
		return $html; // and finally, return $html
    }

    private function log($msgtoLog){
        $writer = new Zend_Log_Writer_Firebug();
        $logger = new Zend_Log($writer);
        $logger->log($msgtoLog, Zend_Log::INFO);
    }   
 
    
    /**
     * kthen nje array me gjithe indikatoret qe kane nje skedar koresponues
     * return @array     
     */
    private function getIndFiles(){
		$xml_dir = '/var/www/vhosts/data.al/subdomains/open/httpdocs/semanticfiles/xml/';	
		$files = array();
		$nofiles=0;			
		// Open a known directory, and proceed to read its contents
		if (is_dir($xml_dir)) {
		    if ($dh = opendir($xml_dir)) {
			//
		        while (($file = readdir($dh)) !== false) {
		          if ($file != '.' && $file != '..' ){
		          	$filepath=pathinfo($file);			
			        $ext = $filepath["extension"];
			        $base = $filepath["filename"];
			        if ($ext=="xml"){						
						$files[$nofiles]= $base;    
						$nofiles++;
			        }            
			      }  
		        }
		        return $files;
		        closedir($dh);
		    }
		}
    }
	
	
    
  //PJESA E KATALOGUT  
  
    
   /**
     * metadata per elementet e katalogut
     * return @array     
     */
    private function getMetaDataIndCatalog($indicator){
        $query="PREFIX  dc: <http://purl.org/dc/elements/1.1/>
                SELECT  ?title ?publisher ?creator ?date_created
                FROM <http://open.data.al/semanticfiles/xml/".$indicator.".xml>
                WHERE   {
                ?titull dc:title ?title  .
                ?burimi dc:publisher ?publisher.
                ?krijuesi dc:creator ?creator.
                ?data dc:date ?date_created.
                }";   

         $url="http://www.qcrumb.com/sparql?query=".urlencode($query)."&rules=&accept=text/comma-separated-values";
         $result = $this->retrieveContent($url);
         $matches = preg_split("/((\r(?!\n))|((?<!\r)\n)|(\r\n))/", $result);
         $metadata=explode(',',$matches[1]);
         
         return $metadata;
    }
    
    
    /**
     * do ndryshuar
     * Kthen gjithe indikatoret
     * e perdor te katalogu
     * return @array
     */
    private function retrieveAllIndicators(){
        $query="PREFIX rdf:<http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                PREFIX oda:<http://open.data.al/oda2.owl#>
                SELECT ?indicator
                FROM <http://open.data.al/oda03.owl#>
                WHERE {
                  ?indicator rdf:type oda:Indicator .
                  ?indicator oda:topic ?topic .
                  ?topic rdf:type oda:Topic .
                }
                GROUP BY ?topic ?indicator
                ORDER BY ASC(?topic) ASC(?indicator)";        
        $url="http://www.qcrumb.com/sparql?query=".urlencode($query)."&rules=&accept=";
        $result = $this->retrieveContent($url);       
        preg_match_all("|<http://open.data.al/oda2.owl#(.*)>|U",$result,$match);
        return $match[1];
    }
    
    
   /**
     * Shfaq katalog (listim)
     * 
     */
    public function catalogAction(){
        //$this->_helper->layout()->disableLayout();
        $bootstrap = $this->getInvokeArg ( 'bootstrap' );
		$options = $bootstrap->getOptions ();
		$cache = $bootstrap->getResource ( 'cache' );

        $filterChain = new Zend_Filter ( );
	$filterChain->addFilter ( new Zend_Filter_Alnum ( ) );  
        //tab me gjithe skedaret e indikatoreve ne direktori
		if (! $ind_arr = $cache->load ( 'AllIndArray' )) {
		    $ind_arr = $this->getIndFiles();
			$cache->save ( $ind_arr, 'AllIndArray' );
		}	
		
if ($_SERVER["REMOTE_ADDR"]=="141.66.19.31"){
echo "IN";
//$ind_arr = $this->getIndFiles();
//print_r ($ind_arr);
echo "OUT";
}



//Kthen gjithe indikatoret e katalogut
        if (! $katalogu = $cache->load ( 'Katalogu' )) {
		    $all_indicators=$this->retrieveAllIndicators();
		    $katalogu = array();
			$nofiles = 0;
			foreach ($all_indicators as $ind) :
			   if (in_array($ind, $ind_arr)) {				   		   
			      $katalogu[$nofiles]["inds"]= $ind;   
			      $metadata = $this->getMetaDataIndCatalog("$ind");			     
			      $katalogu[$nofiles]["metadt"]=$metadata;
			      $nofiles++; 
			   }	
			   
			endforeach;
			$cache->save ( $katalogu, 'Katalogu' );
		}		
		$paginator = Zend_Paginator::factory ( $katalogu );
    	$paginator->setItemCountPerPage ( 14 );
    	if ($this->_hasParam ( 'faqe' )) {
    		$paginator->setCurrentPageNumber ( $filterChain->filter ( $this->_getParam ( 'faqe' ) ) );
    	} 
    	else {
    	    $paginator->setCurrentPageNumber ( 1 );
    	}
    	$this->view->paginator = $paginator;
		//$this->view->katalogu=$katalogu;
    	
    }
    
    //FUND PJESA E KATALOGUT
    
    
    
   /**
     * KOMENTE
     * testoj formen
     * return @array
     */
    public function commAction(){
    	$filterChain = new Zend_Filter ( );
		$filterChain->addFilter ( new Zend_Filter_Alnum ( ) );
		print_r ("U dergua");
		if ($this->_request->isPost ()) {
			$formData = $this->_request->getPost ();
    		//get form data    		
		}	
    	
    }
    
 	    private function getInd2Files(){
		$xml_dir = '/var/www/vhosts/data.al/subdomains/explorer/application/';	
	
		// Open a known directory, and proceed to read its contents
		print_r (scandir ($xml_dir));
		$files = array();
		$nofiles=0;			
		// Open a known directory, and proceed to read its contents
		if (is_dir($xml_dir)) {
		print_r ("test");
		    if ($dh = opendir($xml_dir)) {
			//
		        while (($file = readdir($dh)) !== false) {
		          if ($file != '.' && $file != '..' ){
		          	$filepath=pathinfo($file);			
			        $ext = $filepath["extension"];
			        $base = $filepath["filename"];
			        if ($ext=="xml"){						
						$files[$nofiles]= $base;    
						$nofiles++;
			        }            
			      }  
		        }
		        return $files;
		        closedir($dh);
		    }
		}

    }
	
   /**
     * per testime
     * 
     * return @array
     */
    public function testAction(){
			      $metadata = $this->getInd2Files();
			      print_r($metadata);
       
  
    }
    
    
       /**
     * per testime
     * testoj formen
     * return @array
     */
    public function test1Action(){
    	
    }
    
    
    
    
}


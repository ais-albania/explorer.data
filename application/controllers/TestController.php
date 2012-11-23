<?php

class TestController extends Zend_Controller_Action
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
     * 
     * 
     */
    public function sparklistAction(){    
    	// $this->_helper->layout()->disableLayout();    
        $bootstrap = $this->getInvokeArg ( 'bootstrap' );
		$options = $bootstrap->getOptions ();
		$cache = $bootstrap->getResource ( 'cache' );
		$query=$this->sparkQuery();
        $this->view->query = $query;
    }
    
    
    /**
     * 
     * 
     */
    public function sparktableAction(){    
    	$this->_helper->layout()->disableLayout();    

    }
    
    
    /**
     * 
     * 
     */
    public function sparkpiechartAction(){    
    	$this->_helper->layout()->disableLayout();    
        $bootstrap = $this->getInvokeArg ( 'bootstrap' );
		$options = $bootstrap->getOptions ();
		$cache = $bootstrap->getResource ( 'cache' );
		$query=$this->sparkQuery();
        $this->view->query = $query;
    }

    
    
    /**
     * 
     * 
     */
    public function sparkgraphAction(){    
    	// $this->_helper->layout()->disableLayout();    
        $bootstrap = $this->getInvokeArg ( 'bootstrap' );
		$options = $bootstrap->getOptions ();
		$cache = $bootstrap->getResource ( 'cache' );
		$query=$this->sparkQuery();
        $this->view->query = $query;
    }
    
    
    
    private function sparkQuery(){
        $sparkquery="PREFIX rdf:<http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                                          PREFIX oda:<http://open.data.al/oda.owl#>
                                          SELECT ?topic
                                          FROM <http://open.data.al/oda.owl#>
                                          WHERE { 
                                              ?topic rdf:type oda:Topic .
                                          }";
        return $sparkquery;
    }
    /**
     * Retrieve indicators of a speficic topic in ajax request
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

		$topicname="Indicatorsfor".$topic;
        if (! $indicators = $cache->load ( $topicname )) {
		    $indicators=$this->retrieveIndicator($topic);
			$cache->save ( $indicators, $topicname );
		}

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
		    $indicators=$this->retrieveYearsForIndicator($indicator);
			$cache->save ( $years, $yearsForIndicator );
		}
		$this->view->years=$years;

    }



    /**
     * Connect to a remote server via Curl and retrieve web content
     **/
    private function retrieveContent($url) {
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
}


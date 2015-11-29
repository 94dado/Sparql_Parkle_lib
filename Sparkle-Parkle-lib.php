<?php
/*Author: Quadrelli Davide */

class Sparql_Parkle_lib{
	//attributes
	private $server; //the sparql server uri
	private $mode; 	 //the method that the library will show the answer of the server.
	public const $SPARQL_PARKLE_NORMAL_MODE="normal";
	public const $SPARQL_PARKLE_DEBUG_MODE="debug";
	public const $SPARQL_PARKLE_TYPE_QUERY="ask";
	public const $SPARQL_PARKLE_TYPE_UPDATE="update";

	//constructor: initialize the variables
	function __construct($server,$mode="normal"){
		$this->server=$server;
		$this->mode=$mode;
	}

	//the only public method to ask query to the sparql point
	public function sparql_query($type,$query,$format=null){
		var $toret;
		switch ($type){
			case $this->SPARQL_PARKLE_TYPE_QUERY:
				if($format!=null)$toret=sparql_select_describe($query,$format);
				else $toret=sparql_select_describe($query);
				break;
			case $this->SPARQL_PARKLE_TYPE_UPDATE:
				$toret=sparql_insert_delete($query);
				break;
		}
		return $toret;
	}

	//method for the UPDATE, DELETE and CLEAR queries
	private function sparql_insert_delete($query){
		$url=$this->server;
		//array contenete i parametri della richiesta
		$data = array('update' => $query);
		//array contente le intestazioni della richiesta
		$options = array(
		    'http' => array(
		        'header'  => "Content-type: application/x-www-form-urlencoded",
		        'method'  => 'POST',
		        'content' => http_build_query($data),
		    ),
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		
		if($this->mode==$this->SPARQL_PARKLE_DEBUG_MODE) var_dump($result);
		return $result;
	}

	//method for the SELECT and DESRIBE queries
	private function sparql_select_describe($query,$format=NULL){
		$url=$this->server;
		$query=urlencode($query);
		$opts = array(
	  	'http'=>array(
	    	'method'=>"GET",
	    	'header'=>"Accept-language: en\r\n" .
	              "Cookie: foo=bar\r\n"
	  		)
		);
		$context = stream_context_create($opts);
		//imposto l'URL delle chiamate con i propri parametri in GET
		$url.='?query='.$query;
		if($format!=NULL){
			$url.='&format='.$format
		}
		$result=file_get_contents($url,false,$context);
		if($this->mode==$this->SPARQL_PARKLE_DEBUG_MODE) var_dump($result);
		return $result;
	}
}

